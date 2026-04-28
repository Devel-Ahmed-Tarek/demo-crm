<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LeadApiController extends Controller
{
    /**
     * POST /api/v1/leads — إنشاء ليد (مفتاح API مطلوب).
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->sanitizeAutomationLeadPayload($request);
            $this->normalizeLeadSourceAndStage($request);

            $stageKeys = LeadStage::query()->orderBy('sort_order')->pluck('key')->filter()->values()->all();
            $sourceKeys = LeadSource::query()->orderBy('sort_order')->pluck('key')->filter()->values()->all();

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email'],
                'phone' => ['nullable', 'string', 'max:255'],
                'source' => empty($sourceKeys)
                    ? ['required', 'string', 'max:255']
                    : ['required', 'string', Rule::in($sourceKeys)],
                'stage' => empty($stageKeys)
                    ? ['required', 'string', 'max:255']
                    : ['required', 'string', Rule::in($stageKeys)],
                'notes' => ['nullable', 'string'],
                'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
                'tags' => ['nullable', 'array'],
                'tags.*' => ['integer', 'exists:lead_tags,id'],
                'team_id' => ['nullable', 'integer', 'exists:teams,id'],
                'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            ]);

            $this->finalizeStoredLeadKeys($validated);

            $teamId = $validated['team_id'] ?? null;
            if ($teamId === null && ! empty($validated['assigned_to'])) {
                $assignee = User::find($validated['assigned_to']);
                $teamId = $assignee?->primaryTeam?->id;
            }
            $validated['team_id'] = $teamId;

            $lead = Lead::create($validated);

            if ($request->filled('tags')) {
                $lead->tags()->sync($request->input('tags'));
            }

            $stage = LeadStage::where('key', $validated['stage'])->first();
            if ($stage && $stage->is_contract_stage) {
                Contract::create([
                    'lead_id' => $lead->id,
                    'customer_id' => $lead->customer_id,
                    'assigned_to' => $lead->assigned_to,
                    'status' => 'draft',
                    'contract_date' => now(),
                ]);
            }

            $lead->load('tags');

            logger()->info('Lead API: ليد تم إنشاؤه', [
                'lead' => $lead->toArray(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead created.',
                'data' => [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'source' => $lead->source,
                    'stage' => $lead->stage,
                    'notes' => $lead->notes,
                    'assigned_to' => $lead->assigned_to,
                    'team_id' => $lead->team_id,
                    'customer_id' => $lead->customer_id,
                    'tags' => $lead->tags->pluck('id')->values()->all(),
                    'created_at' => $lead->created_at?->toIso8601String(),
                ],
            ], 201);
        } catch (ValidationException $e) {
            logger()->warning('Lead API: فشل التحقق (validation)', [
                'errors' => $e->errors(),
                'payload' => $request->all(),
            ]);

            throw $e;
        } catch (\Throwable $e) {
            logger()->error('Lead API: خطأ غير متوقع', [
                'message' => $e->getMessage(),
                'exception' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => app()->environment('local', 'staging')
                    ? $e->getMessage()
                    : __('تعذر إنشاء الليد. تحقق من السجلات.'),
            ], 500);
        }
    }

    /**
     * GET /api/v1/lead-metadata — أرقام id و keys للمصدر والمرحلة (للتكامل: source_id + stage_id ثابتين).
     */
    public function metadata(): JsonResponse
    {
        $sources = LeadSource::query()
            ->orderBy('sort_order')
            ->get(['id', 'key', 'label_en', 'label_ar', 'is_active', 'sort_order']);
        $stages = LeadStage::query()
            ->orderBy('sort_order')
            ->get(['id', 'key', 'label_en', 'label_ar', 'is_contract_stage', 'sort_order']);

        return response()->json([
            'success' => true,
            'message' => 'Use these ids in your integration (source_id, stage_id) or the key strings.',
            'data' => [
                'sources' => $sources,
                'stages' => $stages,
                'example_payload' => [
                    'name' => 'Client name',
                    'phone' => '2010...',
                    'source_id' => $sources->firstWhere('key', 'whatsapp')?->id ?? $sources->first()?->id,
                    'stage_id' => $stages->firstWhere('key', 'new-client')?->id ?? $stages->first()?->id,
                    'notes' => null,
                ],
            ],
        ]);
    }

    /**
     * تأكيد تخزين مفاتيح مصدر/مرحلة كما هي في الداتابيس (حروف لا تنهار في ENUM؛ العمود source أصبح varchar).
     */
    protected function finalizeStoredLeadKeys(array &$validated): void
    {
        $validated['source'] = $this->resolveSourceKeyForDb((string) ($validated['source'] ?? ''));
        $validated['stage'] = $this->resolveStageKeyForDb((string) ($validated['stage'] ?? ''));
    }

    protected function resolveSourceKeyForDb(string $incoming): string
    {
        $trim = trim($incoming);
        if ($trim === '') {
            return $this->firstOrFallbackSourceKey();
        }

        $canon = $this->canonicalSourceFromString($trim);
        if ($canon !== null) {
            return $canon;
        }

        $row = LeadSource::query()->whereRaw('LOWER(`key`) = ?', [mb_strtolower($trim, 'UTF-8')])->first();
        if ($row !== null) {
            return $row->key;
        }

        return $this->firstOrFallbackSourceKey();
    }

    protected function firstOrFallbackSourceKey(): string
    {
        $key = config('lead_api.default_source_key', 'other');

        return LeadSource::query()->where('key', $key)->value('key')
            ?? LeadSource::query()->orderBy('sort_order')->value('key')
            ?? 'other';
    }

    protected function resolveStageKeyForDb(string $incoming): string
    {
        $trim = trim($incoming);
        if ($trim === '') {
            return $this->firstOrFallbackStageKey();
        }

        $canon = $this->canonicalStageFromString($trim);
        if ($canon !== null) {
            return $canon;
        }

        $row = LeadStage::query()->whereRaw('LOWER(`key`) = ?', [mb_strtolower($trim, 'UTF-8')])->first();
        if ($row !== null) {
            return $row->key;
        }

        return $this->firstOrFallbackStageKey();
    }

    protected function firstOrFallbackStageKey(): string
    {
        $key = config('lead_api.default_stage_key', 'new-client');

        return LeadStage::query()->where('key', $key)->value('key')
            ?? LeadStage::query()->orderBy('sort_order')->value('key')
            ?? 'new-client';
    }

    /**
     * يصل من أتمتة خارجية (مثل WhatsApp AI) payloads خاطئة: نص "ثابت: null" بدل JSON null، أو "ثابت: WhatsApp".
     */
    protected function sanitizeAutomationLeadPayload(Request $request): void
    {
        foreach (['name', 'email', 'phone', 'notes', 'source', 'stage', 'assigned_to', 'team_id', 'customer_id', 'tags'] as $key) {
            if (! $request->exists($key)) {
                continue;
            }
            $v = $request->input($key);
            if (is_string($v)) {
                $v = $this->unwrapPrefixLabelValue($v);
            }
            if (in_array($key, ['name', 'email', 'phone', 'notes'], true)) {
                $request->merge([$key => $v]);

                continue;
            }
            if ($key === 'tags') {
                $request->merge(['tags' => $this->coerceTagsPayload($v)]);

                continue;
            }
            if (in_array($key, ['assigned_to', 'team_id', 'customer_id'], true)) {
                $request->merge([$key => $this->coerceNullableInt($v)]);

                continue;
            }

            $request->merge([$key => $v]);
        }
    }

    protected function unwrapPrefixLabelValue(string $value): string
    {
        $t = trim($value);
        if ($t === '') {
            return '';
        }
        // "ثابت: قيمة" أو أي بادئة نص قبل أول `:`
        if (preg_match('/^[^:]+:\s*(.*)$/su', $t, $m)) {
            return trim((string) $m[1]);
        }

        return $t;
    }

    /**
     * @return mixed int|null أو رقم مستخدم موجود
     */
    protected function coerceNullableInt(mixed $v): ?int
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_int($v)) {
            return $v;
        }
        if (is_float($v)) {
            return (int) $v;
        }
        $s = strtolower(trim((string) $v));
        if (in_array($s, ['null', 'undefined', 'nil', '(null)', '[null]', '—', '-'], true)) {
            return null;
        }

        return filter_var(trim((string) $v), FILTER_VALIDATE_INT,
            FILTER_NULL_ON_FAILURE);
    }

    protected function coerceTagsPayload(mixed $v): array
    {
        if ($v === null || $v === '') {
            return [];
        }
        if (is_array($v)) {
            return array_values(array_map(static fn ($x) => (int) $x, $v));
        }
        if (! is_string($v)) {
            return [];
        }
        $s = trim(strtolower((string) $v));
        if ($s === '' || $s === 'null' || $s === '[]') {
            return [];
        }
        if (str_starts_with(trim($v), '[')) {
            $decoded = json_decode(trim($v), true);

            return is_array($decoded) ? array_values(array_map(static fn ($x) => (int) $x, $decoded)) : [];
        }

        return [];
    }

    /**
     * توحيد source/stage قبل التحقق: يدعم source_id/stage_id، مطابقة key بدون حساسية لحالة الأحرف،
     * محارف _ ↔ - ، ومطابقة label_en / label_ar (للمكاملات مثل WhatsApp AI).
     */
    protected function normalizeLeadSourceAndStage(Request $request): void
    {
        if ($request->filled('source_id')) {
            $row = LeadSource::query()->find((int) $request->input('source_id'));
            if ($row !== null) {
                $request->merge(['source' => $row->key]);
            }
        }
        if ($request->filled('stage_id')) {
            $row = LeadStage::query()->find((int) $request->input('stage_id'));
            if ($row !== null) {
                $request->merge(['stage' => $row->key]);
            }
        }

        if ($request->filled('source') && is_string($request->input('source'))) {
            $canonical = $this->canonicalSourceFromString(trim((string) $request->string('source')));
            if ($canonical !== null) {
                $request->merge(['source' => $canonical]);
            }
        }
        if ($request->filled('stage') && is_string($request->input('stage'))) {
            $canonical = $this->canonicalStageFromString(trim((string) $request->string('stage')));
            if ($canonical !== null) {
                $request->merge(['stage' => $canonical]);
            }
        }
    }

    protected function canonicalSourceFromString(string $raw): ?string
    {
        $rows = LeadSource::query()->orderBy('sort_order')->get(['key', 'label_en', 'label_ar']);
        if ($rows->isEmpty()) {
            return null;
        }

        foreach ($rows as $src) {
            if ($this->stringMatchesLeadKeyVariants($raw, $src->key)) {
                return $src->key;
            }
        }

        $norm = $this->normalizeLabelCompare($raw);
        foreach ($rows as $src) {
            foreach ([$src->label_en, $src->label_ar] as $label) {
                if ($label !== null && $label !== '' && $norm === $this->normalizeLabelCompare($label)) {
                    return $src->key;
                }
            }
        }

        if ($this->stringImpliesWhatsAppSource($norm, $raw)) {
            foreach (['whatsapp', 'social', 'other', 'website'] as $tryKey) {
                $fallback = LeadSource::query()->where('key', $tryKey)->first();
                if ($fallback !== null) {
                    return $fallback->key;
                }
            }
        }

        return null;
    }

    protected function stringImpliesWhatsAppSource(string $normalizedKeyish, string $raw): bool
    {
        $r = mb_strtolower($raw, 'UTF-8');

        return str_contains($normalizedKeyish, 'whatsapp')
            || str_contains($normalizedKeyish, 'واتس')
            || str_contains($normalizedKeyish, 'واتساب')
            || str_contains($r, 'whatsapp')
            || str_contains($r, ' واتس');
    }

    protected function canonicalStageFromString(string $raw): ?string
    {
        $rows = LeadStage::query()->orderBy('sort_order')->get(['key', 'label_en', 'label_ar']);
        if ($rows->isEmpty()) {
            return null;
        }

        foreach ($rows as $st) {
            if ($this->stringMatchesLeadKeyVariants($raw, $st->key)) {
                return $st->key;
            }
        }

        $norm = $this->normalizeLabelCompare($raw);
        foreach ($rows as $st) {
            foreach ([$st->label_en, $st->label_ar] as $label) {
                if ($label !== null && $label !== '' && $norm === $this->normalizeLabelCompare($label)) {
                    return $st->key;
                }
            }
        }

        return null;
    }

    protected function stringMatchesLeadKeyVariants(string $raw, string $key): bool
    {
        if (strcasecmp($raw, $key) === 0) {
            return true;
        }
        foreach ([str_replace('_', '-', $raw), str_replace('-', '_', $raw)] as $variant) {
            if (strcasecmp((string) $variant, $key) === 0) {
                return true;
            }
            if (strcasecmp((string) $variant, str_replace('_', '-', $key)) === 0) {
                return true;
            }
        }

        return false;
    }

    protected function normalizeLabelCompare(string $s): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($s)), 'UTF-8');
    }
}

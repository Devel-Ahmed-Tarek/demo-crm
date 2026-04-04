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

class LeadApiController extends Controller
{
    /**
     * POST /api/v1/leads — إنشاء ليد (مفتاح API مطلوب).
     */
    public function store(Request $request): JsonResponse
    {
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
    }
}

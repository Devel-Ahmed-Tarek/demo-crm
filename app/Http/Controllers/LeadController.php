<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesTeamVisibility;
use App\Models\Lead;
use App\Models\LeadTag;
use App\Models\LeadStage;
use App\Models\LeadSource;
use App\Models\LeadComment;
use App\Models\LeadActivity;
use App\Models\Contract;
use App\Models\User;
use App\Models\CustomerCommunication;
use App\Notifications\LeadAssigned;
use App\Notifications\LeadEventMissed;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    use HandlesTeamVisibility;

    protected ?Collection $leadStagesCache = null;
    protected ?array $leadStageKeysCache = null;
    protected ?Collection $leadSourcesCache = null;
    protected ?array $leadSourceKeysCache = null;

    public function index(Request $request)
    {
        $viewType = $request->get('view', session('leads_view', 'list'));
        session(['leads_view' => $viewType]);

        $leadStages = $this->leadStages();
        $leadSources = $this->leadSources();
        $stageMeta = $this->normalizedStageMeta();
        $activeSources = $leadSources->filter(fn(LeadSource $source) => $source->is_active ?? true);
        $sourceLabels = $this->sourceLabels();
        $user = $request->user();
        $teams = $this->availableTeamsFor($user);

        $query = Lead::with(['assignedUser', 'customer', 'tags', 'team'])
            ->when($viewType === 'board', function ($builder) {
                $builder->with(['activities' => function ($activityQuery) {
                    $activityQuery->latest()->limit(3);
                }]);
            });

        $this->applyTeamScope($query, $user);

        // Filters
        if ($request->has('source') && $request->source) {
            $query->where('source', $request->source);
        }

        if ($request->has('stage') && $request->stage) {
            $query->where('stage', $request->stage);
        }

        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->has('tag') && $request->tag) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('lead_tags.id', $request->tag);
            });
        }

        if ($request->has('created_today') && $request->created_today == '1') {
            $query->whereDate('created_at', now()->toDateString());
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(created_at, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(created_at, '%Y/%m/%d') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(updated_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(updated_at, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(updated_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(updated_at, '%Y/%m/%d') LIKE ?", ["%{$search}%"]);
            });
        }

        $listQuery = clone $query;
        $boardQuery = clone $query;

        $leads = null;
        $boardLeads = collect();

        if ($viewType === 'board') {
            $boardLeads = $boardQuery->orderByDesc('updated_at')->get()->groupBy('stage');
        } else {
            $leads = $listQuery->latest()->paginate(20)->withQueryString();
        }
        $users = User::where('is_active', true)->get();
        $tags = LeadTag::all();

        return view('leads.index', [
            'leads' => $leads,
            'users' => $users,
            'tags' => $tags,
            'viewType' => $viewType,
            'stageMeta' => $stageMeta,
            'boardLeads' => $boardLeads,
            'leadStages' => $leadStages,
            'leadSources' => $leadSources,
            'sourceOptions' => $activeSources->mapWithKeys(fn(LeadSource $source) => [$source->key => $source->label])->toArray(),
            'sourceLabels' => $sourceLabels,
            'teams' => $teams,
            'selectedTeamId' => $request->team_id,
        ]);
    }

    public function create()
    {
        $users = User::where('is_active', true)->whereIn('role', ['sales_supervisor', 'sales_agent'])->get();
        $tags = LeadTag::all();
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $leadStages = $this->leadStages();
        $leadSources = $this->leadSources()->filter(fn(LeadSource $source) => $source->is_active ?? true);
        $teams = $this->availableTeamsFor($currentUser);
        return view('leads.create', compact('users', 'tags', 'currentUser', 'leadStages', 'leadSources', 'teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'source' => $this->sourceRules(),
            'stage' => $this->stageRules(),
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:lead_tags,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($currentUser->isSalesAgent()) {
            $validated['assigned_to'] = $currentUser->id;
        }

        $validated['team_id'] = $this->determineTeamId(
            $request->input('team_id'),
            $validated['assigned_to'] ?? null,
            $currentUser
        );

        $lead = Lead::create($validated);

        if ($request->has('tags')) {
            $lead->tags()->sync($request->tags);
        }

        // Send notification if lead is assigned to someone
        if ($lead->assigned_to && $lead->assigned_to !== $currentUser->id) {
            $assignedUser = User::find($lead->assigned_to);
            if ($assignedUser) {
                $assignedUser->notify(new LeadAssigned($lead, $currentUser));
            }
        }

        // Send notification to admins when a new lead is created
        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        foreach ($admins as $admin) {
            if ($admin->id !== $currentUser->id) {
                $admin->notify(new LeadAssigned($lead, $currentUser));
            }
        }

        // Check if the stage is a contract stage
        if (isset($validated['stage'])) {
            $stage = LeadStage::where('key', $validated['stage'])->first();
            if ($stage && $stage->is_contract_stage) {
                // Create contract automatically
                Contract::create([
                    'lead_id' => $lead->id,
                    'customer_id' => $lead->customer_id,
                    'assigned_to' => $lead->assigned_to,
                    'status' => 'draft',
                    'contract_date' => now(),
                ]);
            }
        }

        return $this->redirectToPreferredView()->with('success', 'Lead created successfully');
    }

    public function show(Lead $lead)
    {
        $this->ensureCanAccessLead($lead);
        $lead->load([
            'assignedUser',
            'customer',
            'tags',
            'customer.communications',
            'comments.user',
            'events.user',
            'activities.user'
        ]);
        $leadStages = $this->leadStages();
        $leadSources = $this->leadSources();
        $sourceLabels = $this->sourceLabels();
        return view('leads.show', compact('lead', 'leadStages', 'leadSources', 'sourceLabels'));
    }

    public function edit(Lead $lead)
    {
        $this->ensureCanAccessLead($lead);
        $users = User::where('is_active', true)->whereIn('role', ['sales_supervisor', 'sales_agent'])->get();
        $tags = LeadTag::all();
        $lead->load('tags', 'team');
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $leadStages = $this->leadStages();
        $leadSources = $this->leadSources();
        $teams = $this->availableTeamsFor($currentUser);
        return view('leads.edit', compact('lead', 'users', 'tags', 'currentUser', 'leadStages', 'leadSources', 'teams'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->ensureCanAccessLead($lead);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'source' => $this->sourceRules(),
            'stage' => $this->stageRules(),
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:lead_tags,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($currentUser->isSalesAgent()) {
            $validated['assigned_to'] = $lead->assigned_to;
            $validated['team_id'] = $lead->team_id;
        } else {
            $validated['team_id'] = $this->determineTeamId(
                $request->input('team_id'),
                $validated['assigned_to'] ?? $lead->assigned_to,
                $currentUser
            ) ?? $lead->team_id;
        }

        $oldStage = $lead->stage;
        $oldAssignedTo = $lead->assigned_to;
        $lead->update($validated);

        // Send notification if lead assignment changed
        if (isset($validated['assigned_to']) && $validated['assigned_to'] && $validated['assigned_to'] !== $oldAssignedTo && $validated['assigned_to'] !== $currentUser->id) {
            $assignedUser = User::find($validated['assigned_to']);
            if ($assignedUser) {
                $assignedUser->notify(new LeadAssigned($lead, $currentUser));
            }
        }

        // Check if the new stage is a contract stage
        if (isset($validated['stage']) && $validated['stage'] !== $oldStage) {
            $newStage = LeadStage::where('key', $validated['stage'])->first();
            if ($newStage && $newStage->is_contract_stage) {
                // Check if contract already exists
                $existingContract = Contract::where('lead_id', $lead->id)->first();
                if (!$existingContract) {
                    // Create contract automatically
                    Contract::create([
                        'lead_id' => $lead->id,
                        'customer_id' => $lead->customer_id,
                        'assigned_to' => $lead->assigned_to,
                        'status' => 'draft',
                        'contract_date' => now(),
                    ]);
                }
            }
        }

        if ($request->has('tags')) {
            $lead->tags()->sync($request->tags);
        }

        return $this->redirectToPreferredView()->with('success', 'Lead updated successfully');
    }

    public function destroy(Lead $lead)
    {
        $this->ensureCanAccessLead($lead);
        $lead->delete();
        return $this->redirectToPreferredView()->with('success', 'Lead deleted successfully');
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $this->ensureCanAccessLead($lead);
        $validated = $request->validate([
            'stage' => $this->stageRules(),
        ]);

        $oldStage = $lead->stage;
        $lead->update($validated);

        // Check if the new stage is a contract stage
        $newStage = LeadStage::where('key', $validated['stage'])->first();
        if ($newStage && $newStage->is_contract_stage) {
            // Check if contract already exists
            $existingContract = Contract::where('lead_id', $lead->id)->first();
            if (!$existingContract) {
                // Create contract automatically
                Contract::create([
                    'lead_id' => $lead->id,
                    'customer_id' => $lead->customer_id,
                    'assigned_to' => $lead->assigned_to,
                    'status' => 'draft',
                    'contract_date' => now(),
                ]);
            }
        }

        return response()->json(['success' => true, 'stage' => $lead->stage]);
    }

    public function storeComment(Request $request, Lead $lead)
    {
        $this->ensureCanAccessLead($lead);
        $validated = $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        $lead->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('leads.show', $lead)->with('success', __('Comment added successfully.'));
    }

    public function storeEvent(Request $request, Lead $lead)
    {
        $this->ensureCanAccessLead($lead);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'activity_type' => 'required|in:call,email,meeting,site_visit,note,task,event',
        ]);

        $activity = $lead->activities()->create([
            'user_id' => Auth::id(),
            'activity_type' => $validated['activity_type'],
            'title' => $validated['title'],
            'details' => $validated['details'] ?? null,
            'scheduled_at' => $validated['scheduled_at'],
        ]);

        // Check if event is missed and send notification immediately
        $this->checkMissedEvent($activity);

        return redirect()->route('leads.show', $lead)->with('success', __('Event added successfully.'));
    }

    public function storeActivity(Request $request, Lead $lead)
    {
        $this->ensureCanAccessLead($lead);
        $validated = $request->validate([
            'activity_type' => 'required|in:call,email,meeting,site_visit,note,task',
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'log_communication' => 'nullable|boolean',
            'communication_type' => 'required_if:log_communication,1|in:whatsapp,email,visit,call',
            'communication_completed_at' => 'nullable|date',
        ]);

        $activity = $lead->activities()->create([
            'activity_type' => $validated['activity_type'],
            'title' => $validated['title'],
            'details' => $validated['details'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'user_id' => Auth::id(),
        ]);

        // Check if event is missed and send notification immediately
        if ($activity->scheduled_at) {
            $this->checkMissedEvent($activity);
        }

        $message = 'Activity added successfully.';

        if ($request->boolean('log_communication')) {
            if ($lead->customer_id) {
                CustomerCommunication::create([
                    'customer_id' => $lead->customer_id,
                    'user_id' => Auth::id(),
                    'type' => $validated['communication_type'],
                    'notes' => $validated['details'] ?? $validated['title'],
                    'scheduled_at' => $validated['scheduled_at'] ?? null,
                    'completed_at' => $validated['communication_completed_at'] ?? null,
                ]);

                $message .= ' Logged under customer communications.';
            } else {
                $message .= ' Lead is not linked to a customer, so communication log was skipped.';
            }
        }

        return $this->redirectToPreferredView()->with('success', $message);
    }

    protected function redirectToPreferredView()
    {
        $view = session('leads_view', 'list');
        return redirect()->route('leads.index', ['view' => $view]);
    }

    public function bulkDestroy(Request $request)
    {
        $this->ensureAdmin();

        $ids = collect($request->input('ids', []))->filter()->unique();

        if ($ids->isEmpty()) {
            return $this->redirectToPreferredView()->with('error', 'Please select at least one lead to delete.');
        }

        Lead::whereIn('id', $ids)->delete();

        return $this->redirectToPreferredView()->with('success', 'Selected leads deleted successfully.');
    }

    protected function ensureCanAccessLead(Lead $lead): void
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return;
        }

        if ($user->isSalesAgent() && $lead->assigned_to !== $user->id) {
            abort(403, 'You are not allowed to access this lead.');
        }

        if ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (empty($teamIds) || !$lead->team_id || !in_array($lead->team_id, $teamIds)) {
                abort(403, 'You are not allowed to access this lead.');
            }
        }
    }

    protected function ensureAdmin(): void
    {
        $user = Auth::user();

        if (!$user instanceof User || !$user->isAdmin()) {
            abort(403, 'Only administrators can perform this action.');
        }
    }

    protected function determineTeamId(?int $teamId, ?int $assignedUserId, $currentUser): ?int
    {
        if ($teamId) {
            return $teamId;
        }

        if ($assignedUserId) {
            $assignedUser = User::find($assignedUserId);
            if ($assignedUser?->primaryTeam) {
                return $assignedUser->primaryTeam->id;
            }
        }

        return $currentUser->primaryTeam?->id;
    }

    protected function stageRules(): array
    {
        $rules = ['required'];
        $keys = $this->leadStageKeys();

        if (!empty($keys)) {
            $rules[] = Rule::in($keys);
        } else {
            $rules[] = 'string';
        }

        return $rules;
    }

    protected function sourceRules(): array
    {
        $rules = ['required'];
        $keys = $this->leadSourceKeys();

        if (!empty($keys)) {
            $rules[] = Rule::in($keys);
        } else {
            $rules[] = 'string';
        }

        return $rules;
    }

    protected function leadStages(): Collection
    {
        if ($this->leadStagesCache === null) {
            $stages = LeadStage::orderBy('sort_order')->get();

            if ($stages->isEmpty()) {
                $order = 1;
                $stages = collect($this->legacyStageMeta())->map(function (array $meta, string $key) use (&$order) {
                    return LeadStage::make([
                        'key' => $key,
                        'label_en' => $meta['label_en'],
                        'label_ar' => $meta['label_ar'] ?? null,
                        'description_en' => $meta['description_en'] ?? null,
                        'description_ar' => $meta['description_ar'] ?? null,
                        'accent' => $meta['accent'] ?? null,
                        'dot' => $meta['dot'] ?? null,
                        'border' => $meta['border'] ?? null,
                        'card_border' => $meta['card_border'] ?? null,
                        'shadow' => $meta['shadow'] ?? null,
                        'glow' => $meta['glow'] ?? null,
                        'category' => $meta['category'] ?? 'positive',
                        'sort_order' => $meta['sort_order'] ?? $order++,
                    ]);
                });
            }

            $this->leadStagesCache = $stages;
        }

        return $this->leadStagesCache;
    }

    protected function leadStageKeys(): array
    {
        if ($this->leadStageKeysCache === null) {
            $this->leadStageKeysCache = $this->leadStages()->pluck('key')->toArray();
        }

        return $this->leadStageKeysCache;
    }

    protected function normalizedStageMeta(): array
    {
        $stages = $this->leadStages();

        if ($stages->isEmpty()) {
            return collect($this->legacyStageMeta())->mapWithKeys(function (array $meta, string $key) {
                return [
                    $key => [
                        'label' => $meta['label_en'] ?? ucfirst($key),
                        'description' => $meta['description_en'] ?? '',
                        'accent' => $meta['accent'] ?? 'from-slate-500 to-slate-600',
                        'dot' => $meta['dot'] ?? 'bg-slate-400',
                        'border' => $meta['border'] ?? 'rgba(227, 227, 224, 0.6)',
                        'card_border' => $meta['card_border'] ?? 'rgba(227, 227, 224, 0.6)',
                        'shadow' => $meta['shadow'] ?? 'rgba(15, 15, 15, 0.15)',
                        'glow' => $meta['glow'] ?? 'rgba(59, 130, 246, 0.12)',
                        'category' => $meta['category'] ?? 'positive',
                    ],
                ];
            })->toArray();
        }

        return $stages->mapWithKeys(function (LeadStage $stage) {
            return [
                $stage->key => [
                    'label' => $stage->label,
                    'description' => $stage->description ?? '',
                    'accent' => $stage->accent ?? 'from-slate-500 to-slate-600',
                    'dot' => $stage->dot ?? 'bg-slate-400',
                    'border' => $stage->border ?? 'rgba(227, 227, 224, 0.6)',
                    'card_border' => $stage->card_border ?? 'rgba(227, 227, 224, 0.6)',
                    'shadow' => $stage->shadow ?? 'rgba(15, 15, 15, 0.15)',
                    'glow' => $stage->glow ?? 'rgba(59, 130, 246, 0.12)',
                    'category' => $stage->category ?? 'positive',
                ],
            ];
        })->toArray();
    }

    protected function leadSources(): Collection
    {
        if ($this->leadSourcesCache === null) {
            $sources = LeadSource::orderBy('sort_order')->get();

            if ($sources->isEmpty()) {
                $sources = collect($this->legacySources())->map(function (array $source, string $key) {
                    return LeadSource::make([
                        'key' => $key,
                        'label_en' => $source['label_en'],
                        'label_ar' => $source['label_ar'] ?? null,
                        'description_en' => $source['description_en'] ?? null,
                        'description_ar' => $source['description_ar'] ?? null,
                        'sort_order' => $source['sort_order'] ?? 0,
                        'is_active' => $source['is_active'] ?? true,
                    ]);
                });
            }

            $this->leadSourcesCache = $sources;
        }

        return $this->leadSourcesCache;
    }

    protected function leadSourceKeys(): array
    {
        if ($this->leadSourceKeysCache === null) {
            $this->leadSourceKeysCache = $this->leadSources()->pluck('key')->toArray();
        }

        return $this->leadSourceKeysCache;
    }

    protected function sourceLabels(): array
    {
        return $this->leadSources()->mapWithKeys(function (LeadSource $source) {
            return [$source->key => $source->label];
        })->toArray();
    }

    protected function legacySources(): array
    {
        return [
            'facebook' => [
                'label_en' => 'Facebook',
                'label_ar' => 'فيسبوك',
                'description_en' => 'Generated from Facebook campaigns.',
                'description_ar' => 'قادمة من حملات فيسبوك.',
                'sort_order' => 1,
            ],
            'website' => [
                'label_en' => 'Website',
                'label_ar' => 'الموقع الإلكتروني',
                'description_en' => 'Submitted via website forms.',
                'description_ar' => 'تم إرسالها عبر نماذج الموقع.',
                'sort_order' => 2,
            ],
            'referral' => [
                'label_en' => 'Referral',
                'label_ar' => 'ترشيح',
                'description_en' => 'Referred by existing customers or partners.',
                'description_ar' => 'عن طريق عملاء أو شركاء حاليين.',
                'sort_order' => 3,
            ],
            'other' => [
                'label_en' => 'Other',
                'label_ar' => 'أخرى',
                'description_en' => 'Any custom or ad-hoc source.',
                'description_ar' => 'أي مصدر مخصص أو متنوع.',
                'sort_order' => 4,
            ],
        ];
    }

    protected function legacyStageMeta(): array
    {
        return [
            'new-client' => [
                'label_en' => 'New Client',
                'label_ar' => 'العميل الجديد',
                'description_en' => 'Fresh lead created in the system and awaiting first contact.',
                'description_ar' => 'عميل جديد تمت إضافته ويحتاج إلى التواصل الأول.',
                'accent' => 'from-sky-500 to-cyan-500',
                'dot' => 'bg-sky-500',
                'border' => 'rgba(14, 165, 233, 0.25)',
                'card_border' => 'rgba(6, 182, 212, 0.25)',
                'shadow' => 'rgba(14, 165, 233, 0.25)',
                'glow' => 'rgba(14, 165, 233, 0.15)',
                'sort_order' => 1,
                'category' => 'positive',
            ],
            'converted-client' => [
                'label_en' => 'Converted Client',
                'label_ar' => 'العميل المحوَّل',
                'description_en' => 'Lead converted from another pipeline or team.',
                'description_ar' => 'عميل تم تحويله من مسار أو فريق آخر.',
                'accent' => 'from-blue-500 to-indigo-500',
                'dot' => 'bg-indigo-500',
                'border' => 'rgba(99, 102, 241, 0.3)',
                'card_border' => 'rgba(59, 130, 246, 0.25)',
                'shadow' => 'rgba(79, 70, 229, 0.25)',
                'glow' => 'rgba(79, 70, 229, 0.15)',
                'sort_order' => 2,
                'category' => 'positive',
            ],
            'serious-client' => [
                'label_en' => 'Serious Client',
                'label_ar' => 'العميل الجاد',
                'description_en' => 'Engaged lead showing strong buying signals.',
                'description_ar' => 'عميل يبدي اهتماماً جدياً وإشارات شراء واضحة.',
                'accent' => 'from-emerald-500 to-green-500',
                'dot' => 'bg-emerald-500',
                'border' => 'rgba(16, 185, 129, 0.35)',
                'card_border' => 'rgba(34, 197, 94, 0.3)',
                'shadow' => 'rgba(16, 185, 129, 0.25)',
                'glow' => 'rgba(16, 185, 129, 0.18)',
                'sort_order' => 3,
                'category' => 'positive',
            ],
            'non-serious-client' => [
                'label_en' => 'Non-Serious Client',
                'label_ar' => 'العميل غير الجاد',
                'description_en' => 'Lead engaged but without serious intent.',
                'description_ar' => 'عميل تم التواصل معه لكنه لا يبدي جدية.',
                'accent' => 'from-slate-500 to-slate-600',
                'dot' => 'bg-slate-500',
                'border' => 'rgba(107, 114, 128, 0.3)',
                'card_border' => 'rgba(148, 163, 184, 0.3)',
                'shadow' => 'rgba(107, 114, 128, 0.25)',
                'glow' => 'rgba(107, 114, 128, 0.15)',
                'sort_order' => 4,
                'category' => 'negative',
            ],
            'not-interested' => [
                'label_en' => 'Not Interested',
                'label_ar' => 'غير مهتم',
                'description_en' => 'Lead clearly stated lack of interest.',
                'description_ar' => 'عميل صرّح بأنه غير مهتم حالياً.',
                'accent' => 'from-rose-500 to-pink-500',
                'dot' => 'bg-rose-500',
                'border' => 'rgba(244, 63, 94, 0.3)',
                'card_border' => 'rgba(244, 114, 182, 0.25)',
                'shadow' => 'rgba(190, 24, 93, 0.2)',
                'glow' => 'rgba(244, 63, 94, 0.15)',
                'sort_order' => 5,
                'category' => 'negative',
            ],
            'out-of-segment' => [
                'label_en' => 'Out of Segment',
                'label_ar' => 'خارج الشريحة',
                'description_en' => 'Lead does not match the target profile.',
                'description_ar' => 'عميل لا يتوافق مع الشريحة المستهدفة.',
                'accent' => 'from-gray-400 to-gray-500',
                'dot' => 'bg-gray-400',
                'border' => 'rgba(156, 163, 175, 0.3)',
                'card_border' => 'rgba(209, 213, 219, 0.3)',
                'shadow' => 'rgba(75, 85, 99, 0.25)',
                'glow' => 'rgba(156, 163, 175, 0.15)',
                'sort_order' => 6,
                'category' => 'negative',
            ],
            'no-answer' => [
                'label_en' => 'No Answer',
                'label_ar' => 'لا يرد',
                'description_en' => 'Repeated attempts made but no response yet.',
                'description_ar' => 'تمت محاولات متعددة دون أي رد حتى الآن.',
                'accent' => 'from-amber-400 to-yellow-500',
                'dot' => 'bg-amber-400',
                'border' => 'rgba(251, 191, 36, 0.3)',
                'card_border' => 'rgba(245, 158, 11, 0.25)',
                'shadow' => 'rgba(251, 191, 36, 0.15)',
                'glow' => 'rgba(251, 191, 36, 0.15)',
                'sort_order' => 7,
                'category' => 'negative',
            ],
            'never-answers' => [
                'label_en' => 'Never Answers',
                'label_ar' => 'لا يرد مطلقاً',
                'description_en' => 'Lead consistently unreachable on all channels.',
                'description_ar' => 'عميل لا يمكن التواصل معه على أي قناة.',
                'accent' => 'from-orange-500 to-orange-600',
                'dot' => 'bg-orange-500',
                'border' => 'rgba(249, 115, 22, 0.3)',
                'card_border' => 'rgba(251, 146, 60, 0.25)',
                'shadow' => 'rgba(251, 146, 60, 0.2)',
                'glow' => 'rgba(249, 115, 22, 0.15)',
                'sort_order' => 8,
                'category' => 'negative',
            ],
            'visit' => [
                'label_en' => 'Visit',
                'label_ar' => 'زيارة',
                'description_en' => 'Lead scheduled or completed a site visit.',
                'description_ar' => 'تم تحديد أو تنفيذ زيارة ميدانية مع العميل.',
                'accent' => 'from-indigo-500 to-blue-600',
                'dot' => 'bg-blue-500',
                'border' => 'rgba(79, 70, 229, 0.3)',
                'card_border' => 'rgba(59, 130, 246, 0.25)',
                'shadow' => 'rgba(37, 99, 235, 0.25)',
                'glow' => 'rgba(59, 130, 246, 0.18)',
                'sort_order' => 9,
                'category' => 'positive',
            ],
            'reservation' => [
                'label_en' => 'Reservation',
                'label_ar' => 'حجز',
                'description_en' => 'Lead paid or committed to reserve a unit.',
                'description_ar' => 'قام العميل بدفع أو تأكيد حجز وحدة.',
                'accent' => 'from-teal-500 to-emerald-500',
                'dot' => 'bg-teal-500',
                'border' => 'rgba(20, 184, 166, 0.35)',
                'card_border' => 'rgba(45, 212, 191, 0.3)',
                'shadow' => 'rgba(13, 148, 136, 0.25)',
                'glow' => 'rgba(20, 184, 166, 0.18)',
                'sort_order' => 10,
                'category' => 'positive',
            ],
            'contract' => [
                'label_en' => 'Contract',
                'label_ar' => 'تعاقد',
                'description_en' => 'Lead completed the contracting process.',
                'description_ar' => 'أتم العميل إجراءات التعاقد.',
                'accent' => 'from-green-600 to-lime-500',
                'dot' => 'bg-green-600',
                'border' => 'rgba(34, 197, 94, 0.35)',
                'card_border' => 'rgba(132, 204, 22, 0.3)',
                'shadow' => 'rgba(34, 197, 94, 0.25)',
                'glow' => 'rgba(34, 197, 94, 0.18)',
                'sort_order' => 11,
                'category' => 'positive',
            ],
            'follow-up' => [
                'label_en' => 'Follow-up',
                'label_ar' => 'متابعة',
                'description_en' => 'Lead requires scheduled follow-ups.',
                'description_ar' => 'العميل في مرحلة المتابعة المستمرة.',
                'accent' => 'from-purple-500 to-fuchsia-500',
                'dot' => 'bg-purple-500',
                'border' => 'rgba(168, 85, 247, 0.3)',
                'card_border' => 'rgba(192, 132, 252, 0.3)',
                'shadow' => 'rgba(147, 51, 234, 0.25)',
                'glow' => 'rgba(168, 85, 247, 0.15)',
                'sort_order' => 12,
                'category' => 'positive',
            ],
            'waiting' => [
                'label_en' => 'Waiting',
                'label_ar' => 'انتظار',
                'description_en' => 'Lead is waiting for internal actions or customer decision.',
                'description_ar' => 'العميل في وضع الانتظار لخطوة لاحقة أو قرار.',
                'accent' => 'from-yellow-400 to-amber-500',
                'dot' => 'bg-yellow-400',
                'border' => 'rgba(250, 204, 21, 0.35)',
                'card_border' => 'rgba(252, 211, 77, 0.3)',
                'shadow' => 'rgba(251, 191, 36, 0.2)',
                'glow' => 'rgba(250, 204, 21, 0.18)',
                'sort_order' => 13,
                'category' => 'positive',
            ],
            'refund' => [
                'label_en' => 'Refund',
                'label_ar' => 'استرداد',
                'description_en' => 'Lead requested or received a refund.',
                'description_ar' => 'طلب العميل استرداد أمواله أو حصل عليه.',
                'accent' => 'from-rose-500 to-red-500',
                'dot' => 'bg-red-500',
                'border' => 'rgba(239, 68, 68, 0.3)',
                'card_border' => 'rgba(248, 113, 113, 0.25)',
                'shadow' => 'rgba(220, 38, 38, 0.2)',
                'glow' => 'rgba(248, 113, 113, 0.15)',
                'sort_order' => 14,
                'category' => 'negative',
            ],
            'previous-visit' => [
                'label_en' => 'Previous Visit',
                'label_ar' => 'زيارة سابقة',
                'description_en' => 'Lead completed a visit earlier and needs re-engagement.',
                'description_ar' => 'عميل قام بزيارة سابقة ويحتاج إلى إعادة تواصل.',
                'accent' => 'from-violet-500 to-purple-600',
                'dot' => 'bg-violet-500',
                'border' => 'rgba(139, 92, 246, 0.3)',
                'card_border' => 'rgba(167, 139, 250, 0.3)',
                'shadow' => 'rgba(124, 58, 237, 0.25)',
                'glow' => 'rgba(139, 92, 246, 0.15)',
                'sort_order' => 15,
                'category' => 'positive',
            ],
            'sea-projects' => [
                'label_en' => 'Sea Projects',
                'label_ar' => 'مشروعات بحر',
                'description_en' => 'Lead interested specifically in waterfront or sea projects.',
                'description_ar' => 'عميل يهتم بمشروعات على البحر أو الواجهة البحرية.',
                'accent' => 'from-cyan-500 to-blue-600',
                'dot' => 'bg-cyan-500',
                'border' => 'rgba(14, 165, 233, 0.35)',
                'card_border' => 'rgba(59, 130, 246, 0.25)',
                'shadow' => 'rgba(6, 182, 212, 0.25)',
                'glow' => 'rgba(14, 165, 233, 0.18)',
                'sort_order' => 16,
                'category' => 'positive',
            ],
        ];
    }

    /**
     * Check if event is missed (passed 15 minutes or more) and send notification immediately
     */
    protected function checkMissedEvent(LeadActivity $activity): void
    {
        if (!$activity->scheduled_at) {
            return;
        }

        $now = now();
        $scheduledAt = $activity->scheduled_at;
        $fifteenMinutesAgo = $now->copy()->subMinutes(15);

        // Check if event is missed (passed 15 minutes or more, but within last 7 days)
        if ($scheduledAt->isPast() && $scheduledAt->isBefore($fifteenMinutesAgo) && $scheduledAt->isAfter($now->copy()->subDays(7))) {
            $activity->load(['lead', 'user']);
            $lead = $activity->lead;
            if (!$lead) {
                return;
            }

            $user = $activity->user;
            if (!$user) {
                return;
            }

            // Send notification to the user who created the event
            if ($user->id !== auth()->id()) {
                $user->notify(new LeadEventMissed($activity));
            }

            // Send notification to admins
            $admins = User::where('role', 'admin')->where('is_active', true)->get();
            foreach ($admins as $admin) {
                if ($admin->id !== auth()->id() && $admin->id !== $user->id) {
                    $admin->notify(new LeadEventMissed($activity));
                }
            }

            // Send notification to team leader if user is sales agent
            if ($user->isSalesAgent()) {
                $teamIds = $user->teams()->pluck('teams.id')->toArray();
                if (!empty($teamIds)) {
                    $teamLeaders = User::whereHas('teams', function ($query) use ($teamIds) {
                        $query->whereIn('teams.id', $teamIds)
                            ->where('team_user.membership_type', 'leader');
                    })->where('is_active', true)->get();

                    foreach ($teamLeaders as $leader) {
                        if ($leader->id !== auth()->id() && $leader->id !== $user->id) {
                            $leader->notify(new LeadEventMissed($activity));
                        }
                    }
                }
            }
        }
    }
}

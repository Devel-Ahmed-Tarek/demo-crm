<?php

namespace App\Http\Controllers;

use App\Models\LeadStage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadStageController extends Controller
{
    public function index(): View
    {
        $this->ensureAdmin();
        $stages = LeadStage::orderBy('sort_order')->get();

        return view('lead-stages.index', compact('stages'));
    }

    public function create(): View
    {
        $this->ensureAdmin();
        return view('lead-stages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();
        $validated = $this->validateStage($request);
        LeadStage::create($validated);

        return redirect()
            ->route('lead-stages.index')
            ->with('success', __('Stage created successfully.'));
    }

    public function edit(LeadStage $leadStage): View
    {
        $this->ensureAdmin();
        return view('lead-stages.edit', ['stage' => $leadStage]);
    }

    public function update(Request $request, LeadStage $leadStage): RedirectResponse
    {
        $this->ensureAdmin();
        $validated = $this->validateStage($request, $leadStage->id);
        $validated['is_contract_stage'] = $request->has('is_contract_stage') && $request->is_contract_stage == '1';
        $leadStage->update($validated);

        return redirect()
            ->route('lead-stages.index')
            ->with('success', __('Stage updated successfully.'));
    }

    public function destroy(LeadStage $leadStage): RedirectResponse
    {
        $this->ensureAdmin();
        if ($leadStage->leads()->exists()) {
            return redirect()
                ->route('lead-stages.index')
                ->with('error', __('Cannot delete a stage that is assigned to existing leads.'));
        }

        $leadStage->delete();

        return redirect()
            ->route('lead-stages.index')
            ->with('success', __('Stage deleted successfully.'));
    }

    protected function validateStage(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'key' => [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('lead_stages', 'key')->ignore($id),
            ],
            'label_en' => ['required', 'string', 'max:255'],
            'label_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'accent' => ['nullable', 'string', 'max:255'],
            'dot' => ['nullable', 'string', 'max:255'],
            'border' => ['nullable', 'string', 'max:255'],
            'card_border' => ['nullable', 'string', 'max:255'],
            'shadow' => ['nullable', 'string', 'max:255'],
            'glow' => ['nullable', 'string', 'max:255'],
            'category' => ['required', Rule::in(['positive', 'negative'])],
            'is_contract_stage' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }

    protected function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Only administrators can manage lead stages.');
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadSourceController extends Controller
{
    public function index(): View
    {
        $this->ensureAdmin();

        $sources = LeadSource::orderBy('sort_order')->get();

        return view('lead-sources.index', compact('sources'));
    }

    public function create(): View
    {
        $this->ensureAdmin();

        return view('lead-sources.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $this->validateSource($request);
        LeadSource::create($validated);

        return redirect()->route('lead-sources.index')
            ->with('success', __('Source created successfully.'));
    }

    public function edit(LeadSource $leadSource): View
    {
        $this->ensureAdmin();

        return view('lead-sources.edit', ['source' => $leadSource]);
    }

    public function update(Request $request, LeadSource $leadSource): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $this->validateSource($request, $leadSource->id);
        $leadSource->update($validated);

        return redirect()->route('lead-sources.index')
            ->with('success', __('Source updated successfully.'));
    }

    public function destroy(LeadSource $leadSource): RedirectResponse
    {
        $this->ensureAdmin();

        $linkedLeads = Lead::where('source', $leadSource->key)->exists();
        if ($linkedLeads) {
            return redirect()->route('lead-sources.index')
                ->with('error', __('Cannot delete a source that is assigned to existing leads.'));
        }

        $leadSource->delete();

        return redirect()->route('lead-sources.index')
            ->with('success', __('Source deleted successfully.'));
    }

    protected function validateSource(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'key' => [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('lead_sources', 'key')->ignore($id),
            ],
            'label_en' => ['required', 'string', 'max:255'],
            'label_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }

    protected function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Only administrators can manage lead sources.');
    }
}


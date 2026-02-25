<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\UnitActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectUnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the booking plan grid for a project
     */
    public function index(Request $request, Project $project)
    {
        $project->load(['units' => function($query) {
            $query->orderBy('floor', 'desc')->orderBy('column');
        }, 'site']);

        // Get all floors and columns
        $floors = $project->units->pluck('floor')->filter()->unique()->sort()->values();
        $columns = $project->units->pluck('column')->filter()->unique()->sort()->values();

        // If no units exist, get max floors and columns from request or use defaults
        $maxFloors = $request->input('floors', $floors->max() ?? 8);
        $maxColumns = $request->input('columns', $columns->max() ?? 6);

        // Create grid structure
        $grid = [];
        for ($floor = 0; $floor <= $maxFloors; $floor++) {
            for ($column = 1; $column <= $maxColumns; $column++) {
                $unit = $project->units->firstWhere(function($u) use ($floor, $column) {
                    return $u->floor == $floor && $u->column == $column;
                });
                $grid[$floor][$column] = $unit;
            }
        }

        $customers = Customer::all();

        return view('projects.units.index', compact('project', 'grid', 'maxFloors', 'maxColumns', 'customers'));
    }

    /**
     * Show form to configure grid (floors and columns)
     */
    public function configure(Project $project)
    {
        return view('projects.units.configure', compact('project'));
    }

    /**
     * Store grid configuration
     */
    public function storeConfiguration(Request $request, Project $project)
    {
        $validated = $request->validate([
            'floors' => 'required|integer|min:1|max:50',
            'columns' => 'required|integer|min:1|max:20',
        ]);

        // Store configuration in project (you might want to add a settings column)
        // For now, we'll just redirect to the grid view with the configuration
        return redirect()->route('projects.units.index', [
            'project' => $project->id,
            'floors' => $validated['floors'],
            'columns' => $validated['columns']
        ]);
    }

    /**
     * Show form to create/edit a unit
     */
    public function create(Project $project, Request $request)
    {
        $floor = $request->input('floor');
        $column = $request->input('column');
        $customers = Customer::all();

        return view('projects.units.create', compact('project', 'floor', 'column', 'customers'));
    }

    /**
     * Store a new unit
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:units,code',
            'floor' => 'required|integer|min:0',
            'column' => 'required|integer|min:1',
            'area' => 'required|numeric|min:0',
            'rooms' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,reserved,sold,pending,contracted,owner',
            'description' => 'nullable|string',
            'reserved_by' => 'nullable|exists:customers,id',
            'sold_to' => 'nullable|exists:customers,id',
        ]);

        $validated['project_id'] = $project->id;
        $validated['location'] = $project->title ?? '';

        $unit = Unit::create($validated);

        // Set reserved_at if status is reserved
        if ($validated['status'] === 'reserved' && $validated['reserved_by']) {
            $unit->update([
                'reserved_at' => now(),
                'pending_expires_at' => now()->addDays(4),
            ]);
        }

        // Set contracted_at if status is contracted
        if ($validated['status'] === 'contracted') {
            $unit->update(['contracted_at' => now()]);
        }

        // Log activity
        UnitActivityLog::create([
            'unit_id' => $unit->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => 'Unit created in project grid',
        ]);

        return redirect()->route('projects.units.index', $project)->with('success', __('Unit created successfully'));
    }

    /**
     * Show unit data (for AJAX)
     */
    public function show(Project $project, Unit $unit)
    {
        return response()->json($unit);
    }

    /**
     * Show form to edit a unit
     */
    public function edit(Project $project, Unit $unit)
    {
        $customers = Customer::all();
        return view('projects.units.edit', compact('project', 'unit', 'customers'));
    }

    /**
     * Update a unit
     */
    public function update(Request $request, Project $project, Unit $unit)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:units,code,' . $unit->id,
            'floor' => 'required|integer|min:0',
            'column' => 'required|integer|min:1',
            'area' => 'required|numeric|min:0',
            'rooms' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,reserved,sold,pending,contracted,owner',
            'description' => 'nullable|string',
            'reserved_by' => 'nullable|exists:customers,id',
            'sold_to' => 'nullable|exists:customers,id',
        ]);

        $oldData = $unit->toArray();
        
        // Handle status changes
        if ($validated['status'] === 'reserved' && $unit->status !== 'reserved') {
            $validated['reserved_at'] = now();
            $validated['pending_expires_at'] = now()->addDays(4);
        } elseif ($validated['status'] === 'contracted' && $unit->status !== 'contracted') {
            $validated['contracted_at'] = now();
        } elseif ($validated['status'] === 'available') {
            $validated['reserved_at'] = null;
            $validated['pending_expires_at'] = null;
            $validated['reserved_by'] = null;
        }

        $unit->update($validated);

        // Log activity
        UnitActivityLog::create([
            'unit_id' => $unit->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'description' => 'Unit updated in project grid',
            'old_data' => $oldData,
            'new_data' => $unit->toArray(),
        ]);

        return redirect()->route('projects.units.index', $project)->with('success', __('Unit updated successfully'));
    }

    /**
     * Delete a unit
     */
    public function destroy(Project $project, Unit $unit)
    {
        $unit->delete();
        return redirect()->route('projects.units.index', $project)->with('success', __('Unit deleted successfully'));
    }

    /**
     * Quick update unit status (AJAX)
     */
    public function updateStatus(Request $request, Project $project, Unit $unit)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,reserved,sold,pending,contracted,owner',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $oldStatus = $unit->status;
        
        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'reserved') {
            $updateData['reserved_by'] = $validated['customer_id'] ?? null;
            $updateData['reserved_at'] = now();
            $updateData['pending_expires_at'] = now()->addDays(4);
        } elseif ($validated['status'] === 'contracted') {
            $updateData['contracted_at'] = now();
        } elseif ($validated['status'] === 'available') {
            $updateData['reserved_by'] = null;
            $updateData['reserved_at'] = null;
            $updateData['pending_expires_at'] = null;
        } elseif ($validated['status'] === 'sold' && $validated['customer_id']) {
            $updateData['sold_to'] = $validated['customer_id'];
            $updateData['sold_at'] = now();
        }

        $unit->update($updateData);

        // Log activity
        UnitActivityLog::create([
            'unit_id' => $unit->id,
            'user_id' => Auth::id(),
            'action' => 'status_changed',
            'description' => "Status changed from {$oldStatus} to {$validated['status']}",
            'old_data' => ['status' => $oldStatus],
            'new_data' => ['status' => $validated['status']],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully'),
            'unit' => $unit->fresh(),
        ]);
    }
}


<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\Models\Unit;
use App\Models\UnitFeature;
use App\Models\Customer;
use App\Models\UnitActivityLog;
use App\Models\Project;
use App\Helpers\UploadHelper;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::with(['images', 'features', 'reservedBy', 'soldTo']);

        // Filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('min_area') && $request->min_area) {
            $query->where('area', '>=', $request->min_area);
        }

        if ($request->has('rooms') && $request->rooms) {
            $query->where('rooms', $request->rooms);
        }

        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Only show available and reserved units (hide sold)
        if (!$request->has('status') || $request->status !== 'sold') {
            $query->whereIn('status', ['available', 'reserved']);
        }

        $units = $query->latest()->paginate(20);
        $features = UnitFeature::all();

        return view('units.index', compact('units', 'features'));
    }

    public function create()
    {
        $features = UnitFeature::all();
        $projects = Project::with('site')->orderBy('title')->get();
        return view('units.create', compact('features', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:units,code',
            'location' => 'required|string',
            'area' => 'required|numeric|min:0',
            'rooms' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:available,reserved,sold,contracted,owner',
            'project_id' => 'nullable|exists:projects,id',
            'floor' => 'nullable|integer|min:0',
            'column' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'exists:unit_features,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:102400',
        ]);

        // Handle empty project_id
        if (empty($validated['project_id'])) {
            $validated['project_id'] = null;
        }

        $unit = Unit::create($validated);

        if ($request->has('features')) {
            $unit->features()->sync($request->features);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = UploadHelper::uploadFile('units', $image);
                $unit->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'order' => $index,
                ]);
            }
        }

        // Log activity
        UnitActivityLog::create([
            'unit_id' => $unit->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'description' => 'Unit created',
        ]);

        return redirect()->route('units.index')->with('success', 'Unit created successfully');
    }

    public function show(Unit $unit)
    {
        // return $unit;
        // Ensure unit is not soft deleted
        if ($unit->trashed()) {
            abort(404, 'Unit not found');
        }

        $unit->load(['images', 'features', 'reservedBy', 'soldTo', 'activityLogs.user', 'appointments', 'apartments']);
        $customers = Customer::all();

        // Check if floor and column columns exist
        $hasFloorColumn = \Schema::hasColumns('apartments', ['floor', 'column']);

        // Prepare grid data for apartments
        $grid = [];

        // Use Unit's floor and column as max values, or defaults
        $maxFloors = $unit->floor ?? 7; // Use unit's floor, default 7
        $maxColumns = $unit->column ?? 7; // Use unit's column, default 7

        // Ensure at least 1 floor (0 for ground floor) and 1 column
        $maxFloors = max($maxFloors ?? 0, 0);
        $maxColumns = max($maxColumns ?? 1, 1);

        if ($hasFloorColumn) {
            // Reload apartments with ordering if columns exist
            $unit->load(['apartments' => function ($query) {
                $query->orderBy('floor', 'desc')->orderBy('column');
            }]);

            // Create grid structure - show ALL floors (0 to maxFloors) and ALL columns (1 to maxColumns)
            // This way empty floors and columns still appear
            for ($floor = 0; $floor <= $maxFloors; $floor++) {
                for ($column = 1; $column <= $maxColumns; $column++) {
                    $apartment = $unit->apartments->firstWhere(function ($a) use ($floor, $column) {
                        return $a->floor == $floor && $a->column == $column;
                    });
                    $grid[$floor][$column] = $apartment; // null if no apartment at this position
                }
            }
        } else {
            // Create empty grid structure if columns don't exist
            for ($floor = 0; $floor <= $maxFloors; $floor++) {
                for ($column = 1; $column <= $maxColumns; $column++) {
                    $grid[$floor][$column] = null;
                }
            }
        }
        // return $grid;
        return view('units.show', compact('unit', 'customers', 'grid', 'maxFloors', 'maxColumns', 'hasFloorColumn'));
    }

    public function edit(Unit $unit)
    {
        $features = UnitFeature::all();
        $projects = Project::with('site')->orderBy('title')->get();
        $unit->load(['images', 'features', 'project']);
        return view('units.edit', compact('unit', 'features', 'projects'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:units,code,' . $unit->id,
            'location' => 'required|string',
            'area' => 'required|numeric|min:0',
            'rooms' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:available,reserved,sold,contracted,owner',
            'project_id' => 'nullable|exists:projects,id',
            'floor' => 'nullable|integer|min:0',
            'column' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'exists:unit_features,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:102400',
        ]);

        // Handle empty project_id
        if (empty($validated['project_id'])) {
            $validated['project_id'] = null;
        }

        $oldData = $unit->toArray();
        $unit->update($validated);

        if ($request->has('features')) {
            $unit->features()->sync($request->features);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = UploadHelper::uploadFile('units', $image);
                $unit->images()->create([
                    'image_path' => $path,
                    'is_primary' => false,
                    'order' => $unit->images()->count() + $index,
                ]);
            }
        }

        // Log activity
        UnitActivityLog::create([
            'unit_id' => $unit->id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'description' => 'Unit updated',
            'old_data' => $oldData,
            'new_data' => $unit->toArray(),
        ]);

        return redirect()->route('units.index')->with('success', 'Unit updated successfully');
    }

    public function destroy(Unit $unit)
    {
        $user = auth()->user();
        if ($user && $user->isSalesAgent()) {
            abort(403, 'You are not allowed to delete units.');
        }

        // Delete all associated images
        foreach ($unit->images as $image) {
            UploadHelper::deleteFile($image->image_path);
        }

        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted successfully');
    }

    public function reserve(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'reserved_date' => 'nullable|date',
        ]);

        $oldStatus = $unit->status;

        $unit->update([
            'status' => 'reserved',
            'reserved_by' => $validated['customer_id'] ?? null,
            'reserved_at' => $validated['reserved_date'] ?? now(),
        ]);

        // Log activity
        UnitActivityLog::create([
            'unit_id' => $unit->id,
            'user_id' => auth()->id(),
            'action' => 'reserved',
            'description' => 'Unit reserved' . (!empty($validated['customer_name']) ? " by {$validated['customer_name']}" : ''),
            'old_data' => ['status' => $oldStatus],
            'new_data' => ['status' => 'reserved', 'reserved_by' => $unit->reserved_by],
        ]);

        return redirect()->route('units.index')->with('success', 'Unit reserved successfully');
    }

    public function sell(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sold_date' => 'nullable|date',
            'sales_comment' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $unit->status;

        $unit->update([
            'status' => 'sold',
            'sold_to' => $validated['customer_id'],
            'sold_at' => $validated['sold_date'] ?? now(),
            'sales_comment' => $validated['sales_comment'] ?? null,
        ]);

        // Log activity
        UnitActivityLog::create([
            'unit_id' => $unit->id,
            'user_id' => auth()->id(),
            'action' => 'sold',
            'description' => 'Unit sold',
            'old_data' => ['status' => $oldStatus],
            'new_data' => ['status' => 'sold', 'sold_to' => $unit->sold_to],
        ]);

        return redirect()->route('units.index')->with('success', 'Unit marked as sold successfully');
    }

    public function bulkDestroy(Request $request)
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Only administrators can perform this action.');
        }

        $ids = collect($request->input('ids', []))->filter()->unique();

        if ($ids->isEmpty()) {
            return redirect()->route('units.index')->with('error', 'Please select at least one unit to delete.');
        }

        $units = Unit::with('images')->whereIn('id', $ids)->get();

        foreach ($units as $unit) {
            foreach ($unit->images as $image) {
                UploadHelper::deleteFile($image->image_path);
            }
            $unit->delete();
        }

        return redirect()->route('units.index')->with('success', 'Selected units deleted successfully.');
    }
}

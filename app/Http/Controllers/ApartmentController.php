<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Unit;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function index(Unit $unit)
    {
        $unit->load('apartments');
        return view('apartments.index', compact('unit'));
    }

    public function create(Unit $unit)
    {
        return view('apartments.create', compact('unit'));
    }

    public function store(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'floor' => 'required|integer|min:0',
            'column' => 'required|integer|min:1',
            'code' => 'required|string|unique:apartments,code,NULL,id,unit_id,' . $unit->id,
            'area' => 'nullable|numeric|min:0',
            'rooms' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,reserved,contracted,owner',
            'description' => 'nullable|string',
        ]);

        $validated['unit_id'] = $unit->id;
        Apartment::create($validated);

        return redirect()->route('units.show', $unit)->with('success', __('Apartment created successfully'));
    }

    public function show(Unit $unit, Apartment $apartment)
    {
        if ($apartment->unit_id != $unit->id) {
            abort(404);
        }

        $apartment->load('unit');
        return view('apartments.show', compact('unit', 'apartment'));
    }

    public function edit(Unit $unit, Apartment $apartment)
    {
        if ($apartment->unit_id != $unit->id) {
            abort(404);
        }

        return view('apartments.edit', compact('unit', 'apartment'));
    }

    public function update(Request $request, Unit $unit, Apartment $apartment)
    {
        if ($apartment->unit_id != $unit->id) {
            abort(404);
        }

        $validated = $request->validate([
            'floor' => 'required|integer|min:0',
            'column' => 'required|integer|min:1',
            'code' => 'required|string|unique:apartments,code,' . $apartment->id . ',id,unit_id,' . $unit->id,
            'area' => 'nullable|numeric|min:0',
            'rooms' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,reserved,contracted,owner',
            'description' => 'nullable|string',
        ]);

        $apartment->update($validated);

        return redirect()->route('units.apartments.index', $unit)->with('success', __('Apartment updated successfully'));
    }

    public function destroy(Unit $unit, Apartment $apartment)
    {
        if ($apartment->unit_id != $unit->id) {
            abort(404);
        }

        $apartment->delete();

        return redirect()->route('units.apartments.index', $unit)->with('success', __('Apartment deleted successfully'));
    }
}

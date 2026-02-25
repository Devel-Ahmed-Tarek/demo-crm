<?php

namespace App\Http\Controllers;

use App\Models\LeadTag;
use Illuminate\Http\Request;

class LeadTagController extends Controller
{
    public function index()
    {
        $tags = LeadTag::all();
        return view('lead-tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:lead_tags,name',
            'color' => 'nullable|string|max:7',
        ]);

        LeadTag::create($validated);

        return redirect()->route('lead-tags.index')->with('success', 'Tag created successfully');
    }

    public function destroy(LeadTag $leadTag)
    {
        $leadTag->delete();
        return redirect()->route('lead-tags.index')->with('success', 'Tag deleted successfully');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Lead;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Contract::with(['lead', 'customer', 'unit', 'assignedUser']);

        // Apply user scope
        if ($user->isSalesAgent()) {
            $query->where('assigned_to', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $query->whereHas('lead', function ($leadQuery) use ($teamIds) {
                    $leadQuery->whereIn('team_id', $teamIds);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                    ->orWhereHas('lead', function ($leadQuery) use ($search) {
                        $leadQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $contracts = $query->latest('contract_date')->paginate(20)->withQueryString();
        $users = User::where('is_active', true)->whereIn('role', ['sales_supervisor', 'sales_agent'])->get();
        $units = Unit::where('status', 'available')->get();

        return view('contracts.index', compact('contracts', 'users', 'units'));
    }

    public function show(Contract $contract)
    {
        $contract->load(['lead', 'customer', 'unit', 'assignedUser']);
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $contract->load(['lead', 'customer', 'unit']);
        $users = User::where('is_active', true)->whereIn('role', ['sales_supervisor', 'sales_agent'])->get();
        $units = Unit::all();
        return view('contracts.edit', compact('contract', 'users', 'units'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'contract_number' => 'nullable|string|max:255|unique:contracts,contract_number,' . $contract->id,
            'total_amount' => 'nullable|numeric|min:0',
            'down_payment' => 'nullable|numeric|min:0',
            'remaining_amount' => 'nullable|numeric|min:0',
            'contract_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:draft,active,completed,cancelled',
            'unit_id' => 'nullable|exists:units,id',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ], [
            'end_date.after_or_equal' => __('The end date field must be a date after or equal to start date.'),
        ]);

        $contract->update($validated);

        return redirect()->route('contracts.index')->with('success', __('Contract updated successfully.'));
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return redirect()->route('contracts.index')->with('success', __('Contract deleted successfully.'));
    }
}

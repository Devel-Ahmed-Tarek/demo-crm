<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesTeamVisibility;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use HandlesTeamVisibility;

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Customer::with(['assignedUser', 'team']);
        $this->applyTeamScope($query, $user);

        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(20)->withQueryString();
        $users = User::where('is_active', true)->whereIn('role', ['sales_supervisor', 'sales_agent'])->get();

        return view('customers.index', compact('customers', 'users'));
    }

    public function create()
    {
        $currentUser = auth()->user();
        $users = User::where('is_active', true)->whereIn('role', ['sales_supervisor', 'sales_agent'])->get();
        $teams = $this->availableTeamsFor($currentUser);
        return view('customers.create', compact('users', 'teams', 'currentUser'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $currentUser = $request->user();
        $validated['team_id'] = $this->determineTeamIdForCustomer(
            $request->input('team_id'),
            $validated['assigned_to'] ?? null,
            $currentUser
        );

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully');
    }

    public function show(Customer $customer)
    {
        $this->ensureCanAccessCustomer($customer);
        $customer->load([
            'assignedUser',
            'leads',
            'communications.user',
            'appointments.unit',
            'reservedUnits',
            'purchasedUnits'
        ]);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->ensureCanAccessCustomer($customer);
        $currentUser = auth()->user();
        $users = User::where('is_active', true)->whereIn('role', ['sales_supervisor', 'sales_agent'])->get();
        $teams = $this->availableTeamsFor($currentUser);
        return view('customers.edit', compact('customer', 'users', 'teams', 'currentUser'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $currentUser = $request->user();

        if ($currentUser->isSalesAgent()) {
            $validated['assigned_to'] = $customer->assigned_to;
            $validated['team_id'] = $customer->team_id;
        } else {
            $validated['team_id'] = $this->determineTeamIdForCustomer(
                $request->input('team_id'),
                $validated['assigned_to'] ?? $customer->assigned_to,
                $currentUser
            ) ?? $customer->team_id;
        }

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $this->ensureCanAccessCustomer($customer);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $this->ensureAdmin();

        $ids = collect($request->input('ids', []))->filter()->unique();

        if ($ids->isEmpty()) {
            return redirect()->route('customers.index')->with('error', 'Please select at least one customer to delete.');
        }

        Customer::whereIn('id', $ids)->delete();

        return redirect()->route('customers.index')->with('success', 'Selected customers deleted successfully.');
    }

    protected function ensureAdmin(): void
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Only administrators can perform this action.');
        }
    }

    protected function ensureCanAccessCustomer(Customer $customer): void
    {
        $user = auth()->user();

        if ($user && $user->isSalesAgent() && $customer->assigned_to !== $user->id) {
            abort(403, 'You are not allowed to access this customer.');
        }

        if ($user && $user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (empty($teamIds) || !$customer->team_id || !in_array($customer->team_id, $teamIds)) {
                abort(403, 'You are not allowed to access this customer.');
            }
        }
    }

    protected function determineTeamIdForCustomer(?int $teamId, ?int $assignedUserId, User $currentUser): ?int
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
}

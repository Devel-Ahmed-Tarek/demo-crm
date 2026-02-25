<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        // Check authentication in each method instead of using middleware in constructor
        // Laravel 12 handles middleware differently
    }

    private function checkAuth()
    {
        if (!Auth::check()) {
            abort(401, 'Unauthenticated');
        }
    }

    private function checkAdmin()
    {
        $this->checkAuth();
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAuth();

        $query = User::query();

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount([
            'assignedLeads as total_leads',
            'assignedLeads as active_leads' => function ($query) {
                $query->whereIn('stage', ['new', 'contacted', 'follow-up', 'proposal']);
            },
            'assignedCustomers as total_customers'
        ])->latest()->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->checkAdmin();
        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,sales_supervisor,sales_agent,units_manager',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active') ? true : false;

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function show(User $user)
    {
        $this->checkAuth();

        $twoDaysAgo = now()->subDays(2);

        // Load leads that haven't been contacted or updated in 2+ days
        $user->load([
            'assignedLeads' => function ($query) use ($twoDaysAgo) {
                $query->where(function ($q) use ($twoDaysAgo) {
                    // Leads that haven't been contacted in 2+ days (or never contacted)
                    $q->where(function ($subQ) use ($twoDaysAgo) {
                        $subQ->whereNull('last_contacted_at')
                            ->orWhere('last_contacted_at', '<', $twoDaysAgo);
                    })
                        // AND haven't been updated in 2+ days
                        ->where('updated_at', '<', $twoDaysAgo)
                        // AND don't have any recent activities (within 2 days)
                        ->whereDoesntHave('activities', function ($activityQuery) use ($twoDaysAgo) {
                            $activityQuery->where('created_at', '>=', $twoDaysAgo);
                        });
                })->latest('updated_at')->limit(10);
            },
            'assignedCustomers' => function ($query) {
                $query->latest()->limit(10);
            },
            'appointments' => function ($query) {
                $query->latest()->limit(10);
            },
            'communications' => function ($query) {
                $query->latest()->limit(10);
            }
        ]);

        // Statistics
        $stats = [
            'total_leads' => $user->assignedLeads()->count(),
            'active_leads' => $user->assignedLeads()->whereIn('stage', ['new', 'contacted', 'follow-up', 'proposal'])->count(),
            'won_leads' => $user->assignedLeads()->where('stage', 'won')->count(),
            'total_customers' => $user->assignedCustomers()->count(),
            'total_appointments' => $user->appointments()->count(),
            'total_communications' => $user->communications()->count(),
        ];

        $stats['conversion_rate'] = $stats['total_leads'] > 0
            ? ($stats['won_leads'] / $stats['total_leads']) * 100
            : 0;

        return view('users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        $this->checkAdmin();
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,sales_supervisor,sales_agent,units_manager',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $this->checkAdmin();

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account');
        }

        // Prevent deleting if user has assigned leads or customers
        if ($user->assignedLeads()->count() > 0 || $user->assignedCustomers()->count() > 0) {
            return redirect()->route('users.index')->with('error', 'Cannot delete user with assigned leads or customers. Please reassign them first.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function toggleStatus(User $user)
    {
        $this->checkAdmin();

        // Prevent deactivating yourself
        if ($user->id === auth()->id() && $user->is_active) {
            return redirect()->route('users.index')->with('error', 'You cannot deactivate your own account');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('users.index')->with('success', "User {$status} successfully");
    }

    public function bulkDestroy(Request $request)
    {
        $this->checkAdmin();

        $ids = collect($request->input('ids', []))->filter()->unique();

        if ($ids->isEmpty()) {
            return redirect()->route('users.index')->with('error', 'Please select at least one user to delete.');
        }

        // Prevent deleting yourself
        if ($ids->contains(auth()->id())) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting users with assigned leads or customers
        $usersToDelete = User::whereIn('id', $ids)->get();
        $usersWithAssignments = [];

        foreach ($usersToDelete as $user) {
            if ($user->assignedLeads()->count() > 0 || $user->assignedCustomers()->count() > 0) {
                $usersWithAssignments[] = $user->name;
            }
        }

        if (!empty($usersWithAssignments)) {
            return redirect()->route('users.index')->with('error', 'Cannot delete users with assigned leads or customers: ' . implode(', ', $usersWithAssignments) . '. Please reassign them first.');
        }

        User::whereIn('id', $ids)->delete();

        return redirect()->route('users.index')->with('success', 'Selected users deleted successfully.');
    }
}

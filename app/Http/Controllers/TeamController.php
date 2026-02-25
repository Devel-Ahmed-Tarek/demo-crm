<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount([
            'members',
            'leaders',
            'leads',
            'customers',
        ])->with('leaders')->orderBy('name')->paginate(12);

        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        $this->ensureAdmin();
        [$leaders, $members] = $this->availableMembers();
        return view('teams.create', [
            'leaders' => $leaders,
            'members' => $members,
            'selectedLeaderIds' => [],
            'selectedMemberIds' => [],
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $data = $this->validateTeam($request);

        $team = Team::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'color' => $data['color'] ?? '#6366F1',
            'is_active' => $request->boolean('is_active', true),
            'created_by' => Auth::id(),
        ]);

        $this->syncMembership($team, $data['leaders'] ?? [], $data['members'] ?? []);

        return redirect()->route('teams.index')->with('success', __('Team created successfully.'));
    }

    public function show(Team $team)
    {
        $team->load([
            'members' => fn($q) => $q->orderBy('name'),
            'leads' => fn($q) => $q->latest()->limit(8)->with('assignedUser'),
            'customers' => fn($q) => $q->latest()->limit(8)->with('assignedUser'),
        ])->loadCount([
            'members',
            'leaders',
            'leads',
            'customers',
        ]);

        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $this->ensureAdmin();
        [$leaders, $members] = $this->availableMembers();
        $team->load('members');

        $selectedLeaderIds = $team->members->where('pivot.membership_type', 'leader')->pluck('id')->all();
        $selectedMemberIds = $team->members->where('pivot.membership_type', 'member')->pluck('id')->all();

        return view('teams.edit', compact('team', 'leaders', 'members', 'selectedLeaderIds', 'selectedMemberIds'));
    }

    public function update(Request $request, Team $team)
    {
        $this->ensureAdmin();
        $data = $this->validateTeam($request, $team->id);

        $team->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'color' => $data['color'] ?? '#6366F1',
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->syncMembership($team, $data['leaders'] ?? [], $data['members'] ?? []);

        return redirect()->route('teams.show', $team)->with('success', __('Team updated successfully.'));
    }

    public function destroy(Team $team)
    {
        $this->ensureAdmin();
        if ($team->leads()->exists() || $team->customers()->exists()) {
            return redirect()->route('teams.show', $team)
                ->with('error', __('Cannot delete a team that still has leads or customers assigned.'));
        }

        $team->members()->detach();
        $team->delete();

        return redirect()->route('teams.index')->with('success', __('Team deleted successfully.'));
    }

    protected function validateTeam(Request $request, ?int $teamId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $teamId,
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'leaders' => 'nullable|array',
            'leaders.*' => 'integer|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'integer|exists:users,id',
        ]);
    }

    protected function syncMembership(Team $team, array $leaders, array $members): void
    {
        $pivot = [];

        foreach (array_unique($leaders) as $userId) {
            $pivot[$userId] = ['membership_type' => 'leader'];
        }

        foreach (array_unique($members) as $userId) {
            if (isset($pivot[$userId]) && $pivot[$userId]['membership_type'] === 'leader') {
                continue;
            }

            $pivot[$userId] = ['membership_type' => 'member'];
        }

        $team->members()->sync($pivot);

        if (!empty($pivot)) {
            $attachedIds = array_keys($pivot);

            User::whereIn('id', $attachedIds)->get()->each(function (User $user) use ($team) {
                if ($user->primary_team_id !== $team->id) {
                    $user->primary_team_id = $team->id;
                    $user->save();
                }
            });

            User::where('primary_team_id', $team->id)
                ->whereNotIn('id', $attachedIds)
                ->update(['primary_team_id' => null]);
        } else {
            User::where('primary_team_id', $team->id)->update(['primary_team_id' => null]);
        }
    }

    protected function availableMembers(): array
    {
        $leaders = User::where('is_active', true)
            ->whereIn('role', ['admin', 'sales_supervisor'])
            ->orderBy('name')
            ->get();

        $members = User::where('is_active', true)
            ->where('role', 'sales_agent')
            ->orderBy('name')
            ->get();

        return [$leaders, $members];
    }

    protected function ensureAdmin(): void
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            abort(403, __('Only administrators can manage teams.'));
        }
    }
}

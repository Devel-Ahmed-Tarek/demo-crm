<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait HandlesTeamVisibility
{
    protected function availableTeamsFor(User $user): Collection
    {
        if ($user->isAdmin()) {
            return Team::orderBy('name')->get();
        }

        if ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();

            if (empty($teamIds)) {
                return collect();
            }

            return Team::whereIn('id', $teamIds)->orderBy('name')->get();
        }

        if ($user->isSalesAgent() && $user->primaryTeam) {
            return Team::where('id', $user->primaryTeam->id)->get();
        }

        return collect();
    }

    protected function applyTeamScope(Builder $query, User $user, string $teamColumn = 'team_id'): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isSalesAgent()) {
            return $query->where('assigned_to', $user->id);
        }

        if ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (empty($teamIds)) {
                return $query->whereRaw('1 = 0');
            }

            return $query->whereIn($teamColumn, $teamIds);
        }

        return $query;
    }
}

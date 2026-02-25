<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesTeamVisibility;
use App\Models\Lead;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use HandlesTeamVisibility;

    public function index(Request $request)
    {
        $user = $request->user();
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $leadBaseQuery = $this->applyTeamScope(Lead::query(), $user);

        // Lead reports by status
        $leadStatusReport = (clone $leadBaseQuery)
            ->select('stage', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('stage')
            ->get()
            ->pluck('count', 'stage');

        // Conversion rate
        $totalLeads = (clone $leadBaseQuery)->whereBetween('created_at', [$startDate, $endDate])->count();
        $wonLeads = (clone $leadBaseQuery)->where('stage', 'won')->whereBetween('created_at', [$startDate, $endDate])->count();
        $lostLeads = (clone $leadBaseQuery)->where('stage', 'lost')->whereBetween('created_at', [$startDate, $endDate])->count();
        $conversionRate = ($wonLeads + $lostLeads) > 0 ? ($wonLeads / ($wonLeads + $lostLeads)) * 100 : 0;

        // Dead leads (not contacted in last 30 days)
        $deadLeads = (clone $leadBaseQuery)
            ->whereIn('stage', ['new', 'contacted', 'follow-up'])
            ->where(function($query) {
                $query->whereNull('last_contacted_at')
                    ->orWhere('last_contacted_at', '<', now()->subDays(30));
            })
            ->count();

        // Sales performance by user
        $salesPerformanceQuery = User::whereIn('role', ['sales_supervisor', 'sales_agent']);

        if ($user->isSalesAgent()) {
            $salesPerformanceQuery->where('id', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (empty($teamIds)) {
                $salesPerformanceQuery->whereRaw('1 = 0');
            } else {
                $salesPerformanceQuery->whereHas('teams', function ($teamQuery) use ($teamIds) {
                    $teamQuery->whereIn('teams.id', $teamIds);
                });
            }
        }

        $salesPerformance = $salesPerformanceQuery
            ->withCount([
                'assignedLeads as total_leads' => function($query) use ($startDate, $endDate, $user) {
                    $this->applyTeamScope($query, $user)->whereBetween('created_at', [$startDate, $endDate]);
                },
                'assignedLeads as won_leads' => function($query) use ($startDate, $endDate, $user) {
                    $this->applyTeamScope($query, $user)->where('stage', 'won')->whereBetween('created_at', [$startDate, $endDate]);
                },
            ])
            ->get()
            ->map(function($user) {
                $user->conversion_rate = $user->total_leads > 0 ? ($user->won_leads / $user->total_leads) * 100 : 0;
                return $user;
            });

        // Reports by time period
        $leadsByPeriod = (clone $leadBaseQuery)
            ->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.index', compact(
            'leadStatusReport',
            'conversionRate',
            'deadLeads',
            'salesPerformance',
            'leadsByPeriod',
            'startDate',
            'endDate',
            'totalLeads',
            'wonLeads',
            'lostLeads'
        ));
    }
}

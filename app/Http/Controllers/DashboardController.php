<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\LeadActivity;
use App\Models\Unit;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Contract;
use App\Notifications\LeadEventMissed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $leadQuery = $this->scopedLeadQuery($user);
        $leadStages = $this->leadStages();
        $positiveStages = $leadStages->where('category', 'positive')->pluck('key')->filter()->values();
        if ($positiveStages->isEmpty()) {
            $positiveStages = collect(['new', 'contacted', 'follow-up', 'proposal']);
        }

        // Positive leads (Invoices Awaiting card)
        $totalLeads = (clone $leadQuery)->count();
        $activeLeads = (clone $leadQuery)->whereIn('stage', $positiveStages)->count();
        $positiveLeadPercentage = $totalLeads > 0 ? round(($activeLeads / $totalLeads) * 100) : 0;

        // Today's Leads (Leads created today)
        $todayLeads = (clone $leadQuery)->whereDate('created_at', now()->toDateString())->count();

        // Converted Leads
        $convertedLeads = (clone $leadQuery)->where('stage', 'won')->count();
        $totalLeadsForConversion = (clone $leadQuery)->whereIn('stage', ['won', 'lost'])->count() + $convertedLeads;

        // Projects In Progress (using leads)
        $inProgressLeads = (clone $leadQuery)->whereIn('stage', ['new', 'contacted', 'follow-up', 'proposal'])->count();
        $totalProjects = $totalLeads;

        // Conversion Rate
        $wonLeads = (clone $leadQuery)->where('stage', 'won')->count();
        $totalActiveLeads = (clone $leadQuery)->whereIn('stage', ['won', 'lost'])->count();
        $conversionRate = $totalActiveLeads > 0 ? ($wonLeads / $totalActiveLeads) * 100 : 0;

        // Total Sales
        $unitQuery = $this->scopedUnitQuery($user)->where('status', 'sold');
        $totalSales = $unitQuery->sum('price') ?? 0;
        $salesGrowth = 12; // Percentage

        // Total Users
        $totalUsers = User::where('is_active', true)->count();

        // Total Contracts
        $contractQuery = $this->scopedContractQuery($user);
        $totalContracts = $contractQuery->count();

        // Daily Lead Activities (Today's events)
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $todayEventsQuery = LeadActivity::with(['lead', 'user'])
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$todayStart, $todayEnd]);

        // Apply user scope for lead activities
        if ($user->isSalesAgent()) {
            $todayEventsQuery->whereHas('lead', function ($leadQuery) use ($user) {
                $leadQuery->where('assigned_to', $user->id);
            });
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $todayEventsQuery->whereHas('lead', function ($leadQuery) use ($teamIds) {
                    $leadQuery->whereIn('team_id', $teamIds);
                });
            } else {
                $todayEventsQuery->whereRaw('1 = 0');
            }
        }

        $todayAppointments = $todayEventsQuery->orderBy('scheduled_at', 'asc')->get();

        return view('dashboard', compact(
            'totalLeads',
            'activeLeads',
            'positiveLeadPercentage',
            'todayLeads',
            'convertedLeads',
            'totalLeadsForConversion',
            'inProgressLeads',
            'totalProjects',
            'conversionRate',
            'totalSales',
            'salesGrowth',
            'totalUsers',
            'totalContracts',
            'todayAppointments'
        ));
    }

    public function leadStats(Request $request)
    {
        $user = $request->user();
        $leadQuery = $this->scopedLeadQuery($user);
        $leadStages = $this->leadStages();

        $leadCounts = $leadQuery->select('stage', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('stage')
            ->pluck('aggregate', 'stage');

        $stageGroups = $leadStages->groupBy(fn(LeadStage $stage) => $stage->category ?? 'positive');
        $categoryTotals = $stageGroups->map(function ($stages) use ($leadCounts) {
            return $stages->sum(fn(LeadStage $stage) => $leadCounts[$stage->key] ?? 0);
        });

        $totalVisibleLeads = $leadCounts->sum();

        $categoryLabels = [
            'positive' => __('Positive Leads'),
            'negative' => __('Negative / Cold Leads'),
        ];

        return view('dashboard.lead-stats', [
            'stageGroups' => $stageGroups,
            'leadCounts' => $leadCounts,
            'categoryTotals' => $categoryTotals,
            'totalVisibleLeads' => $totalVisibleLeads,
            'isAdmin' => $user->isAdmin(),
            'categoryLabels' => $categoryLabels,
        ]);
    }

    public function leadsByStage(Request $request, string $stage)
    {
        // Redirect to leads index with stage filter
        return redirect()->route('leads.index', ['stage' => $stage]);
    }

    protected function scopedLeadQuery($user)
    {
        $query = Lead::query();
        if ($user->isSalesAgent()) {
            $query->where('assigned_to', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (empty($teamIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('team_id', $teamIds);
            }
        }
        return $query;
    }

    protected function scopedUnitQuery($user): Builder
    {
        $query = Unit::query();

        if ($user->isSalesAgent()) {
            $query->whereHas('soldTo', function ($customerQuery) use ($user) {
                $customerQuery->where('assigned_to', $user->id);
            });
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (empty($teamIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('soldTo', function ($customerQuery) use ($teamIds) {
                    $customerQuery->whereIn('team_id', $teamIds);
                });
            }
        }

        return $query;
    }

    protected function scopedContractQuery($user): Builder
    {
        $query = Contract::query();

        if ($user->isSalesAgent()) {
            $query->where('assigned_to', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (empty($teamIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('lead', function ($leadQuery) use ($teamIds) {
                    $leadQuery->whereIn('team_id', $teamIds);
                });
            }
        }

        return $query;
    }

    protected function leadStages()
    {
        $stages = LeadStage::orderBy('sort_order')->get();

        if ($stages->isEmpty()) {
            $fallback = [
                ['key' => 'new', 'label_en' => 'New', 'label_ar' => 'جديد', 'category' => 'positive'],
                ['key' => 'contacted', 'label_en' => 'Contacted', 'label_ar' => 'تم التواصل', 'category' => 'positive'],
                ['key' => 'follow-up', 'label_en' => 'Follow-up', 'label_ar' => 'متابعة', 'category' => 'positive'],
                ['key' => 'proposal', 'label_en' => 'Proposal', 'label_ar' => 'عرض', 'category' => 'positive'],
                ['key' => 'won', 'label_en' => 'Won', 'label_ar' => 'مكتمل', 'category' => 'positive'],
                ['key' => 'lost', 'label_en' => 'Lost', 'label_ar' => 'مفقود', 'category' => 'negative'],
            ];

            $stages = collect($fallback)->map(function ($stage) {
                return tap(new LeadStage($stage), function (LeadStage $instance) {
                    $instance->exists = false;
                });
            });
        }

        return $stages;
    }

    /**
     * Get missed lead events (events that passed 15 minutes or more)
     */
    public function missedEvents(Request $request)
    {
        $user = Auth::user();
        $now = now();
        $fifteenMinutesAgo = $now->copy()->subMinutes(15);

        // Get events that passed 15 minutes or more (but within last 7 days)
        $missedEventsQuery = LeadActivity::with(['lead', 'user'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<', $fifteenMinutesAgo)
            ->where('scheduled_at', '>', $now->copy()->subDays(7));

        // Apply user scope
        if ($user->isSalesAgent()) {
            $missedEventsQuery->whereHas('lead', function ($leadQuery) use ($user) {
                $leadQuery->where('assigned_to', $user->id);
            });
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $missedEventsQuery->whereHas('lead', function ($leadQuery) use ($teamIds) {
                    $leadQuery->whereIn('team_id', $teamIds);
                });
            } else {
                $missedEventsQuery->whereRaw('1 = 0');
            }
        }

        $missedEvents = $missedEventsQuery->orderBy('scheduled_at', 'desc')->limit(50)->get();

        // Check and send notifications for missed events that haven't been notified yet
        // This runs when modal is opened to ensure all missed events are notified
        foreach ($missedEvents as $event) {
            $event->load(['lead', 'user']);
            if ($event->lead && $event->user) {
                // Check if notification was already sent (you can add a flag in database if needed)
                // For now, we'll send notification if event passed 15 minutes
                $this->notifyMissedEvent($event);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'events' => $missedEvents->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'details' => $event->details,
                        'scheduled_at' => $event->scheduled_at->format('Y-m-d H:i'),
                        'scheduled_at_formatted' => $event->scheduled_at->format('M d, Y H:i'),
                        'lead_name' => $event->lead->name ?? __('Unknown Lead'),
                        'lead_id' => $event->lead_id,
                        'activity_type' => $event->activity_type,
                        'user_name' => $event->user->name ?? __('Unknown User'),
                    ];
                }),
                'count' => $missedEvents->count(),
            ]);
        }

        return view('dashboard.missed-events', compact('missedEvents'));
    }

    /**
     * Send notification for missed event
     */
    protected function notifyMissedEvent(LeadActivity $activity): void
    {
        $user = $activity->user;
        if (!$user) {
            return;
        }

        $lead = $activity->lead;
        if (!$lead) {
            return;
        }

        // Send notification to the user who created the event
        $user->notify(new LeadEventMissed($activity));

        // Send notification to admins
        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        foreach ($admins as $admin) {
            if ($admin->id !== $user->id) {
                $admin->notify(new LeadEventMissed($activity));
            }
        }

        // Send notification to team leader if user is sales agent
        if ($user->isSalesAgent()) {
            $teamIds = $user->teams()->pluck('teams.id')->toArray();
            if (!empty($teamIds)) {
                $teamLeaders = User::whereHas('teams', function ($query) use ($teamIds) {
                    $query->whereIn('teams.id', $teamIds)
                        ->where('team_user.membership_type', 'leader');
                })->where('is_active', true)->get();

                foreach ($teamLeaders as $leader) {
                    if ($leader->id !== $user->id) {
                        $leader->notify(new LeadEventMissed($activity));
                    }
                }
            }
        }
    }
}

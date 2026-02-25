<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Project;
use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\Partner;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\Service;
use App\Models\HowItWorksStep;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get active partners for the slider
        $partners = Partner::where('is_active', true)
            ->orderBy('order')
            ->get();

        // Get active testimonials
        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('order')
            ->take(6)
            ->get();

        // Get active services
        $services = Service::where('is_active', true)
            ->orderBy('order')
            ->get();

        // Get active how it works steps
        $howItWorksSteps = HowItWorksStep::where('is_active', true)
            ->orderBy('order')
            ->get();

        // Get site settings
        $siteSettings = SiteSetting::all()->keyBy('key');
        $heroImage = $siteSettings['hero_image']->value ?? null;
        $siteName = $siteSettings['site_name']->value ?? 'WE SOLD';
        $siteLogo = $siteSettings['site_logo']->value ?? null;
        $siteFavicon = $siteSettings['site_favicon']->value ?? null;

        return view('home', compact('partners', 'testimonials', 'services', 'howItWorksSteps', 'heroImage', 'siteName', 'siteLogo', 'siteFavicon', 'siteSettings'));
    }

    public function listings(Request $request)
    {
        $query = Site::with(['projects' => function($q) {
            $q->orderBy('type')->orderBy('created_at', 'desc');
        }]);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('projects', function ($projectQuery) use ($search) {
                        $projectQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
            });
        }

        // Filter by project type
        if ($request->has('project_type') && $request->project_type) {
            $query->whereHas('projects', function ($projectQuery) use ($request) {
                $projectQuery->where('type', $request->project_type);
            });
        }

        $sites = $query->latest()->paginate(12);
        $siteSettings = SiteSetting::all()->keyBy('key');

        return view('listings', compact('sites', 'siteSettings'));
                }

    public function siteProjects(Site $site)
    {
        $site->load(['currentProjects.images', 'previousProjects.images']);
        $siteSettings = SiteSetting::all()->keyBy('key');

        return view('site-projects', compact('site', 'siteSettings'));
        }

    public function projectDetails(Site $site, Project $project)
    {
        if ($project->site_id !== $site->id) {
            abort(404);
        }

        $project->load(['images', 'site']);
        $siteSettings = SiteSetting::all()->keyBy('key');

        // Get related projects (same site, same type)
        $relatedProjects = Project::where('site_id', $site->id)
            ->where('type', $project->type)
            ->where('id', '!=', $project->id)
            ->with('images')
            ->take(3)
            ->get();

        return view('project-details', compact('site', 'project', 'siteSettings', 'relatedProjects'));
    }

    public function about()
    {
        $siteSettings = SiteSetting::all()->keyBy('key');
        // Get active testimonials for the about page
        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('order')
            ->take(3)
            ->get();
        return view('about', compact('siteSettings', 'testimonials'));
    }

    public function services()
    {
        $siteSettings = SiteSetting::all()->keyBy('key');
        $services = Service::where('is_active', true)
            ->orderBy('order')
            ->get();
        return view('services', compact('siteSettings', 'services'));
    }

    public function serviceDetails(Service $service)
    {
        if (!$service->is_active) {
            abort(404);
        }

        // Get related services
        $relatedServices = Service::where('id', '!=', $service->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->take(3)
            ->get();

        $siteSettings = SiteSetting::all()->keyBy('key');
        return view('service-details', compact('service', 'relatedServices', 'siteSettings'));
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);

        // Get the first available stage (usually 'new')
        $defaultStage = LeadStage::where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        $stage = $defaultStage ? $defaultStage->key : 'new';

        // Create new lead
        $lead = Lead::create([
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'source' => 'website',
            'stage' => $stage,
            'notes' => $validated['message'] ?? null,
        ]);

        return redirect()->route('contact')->with('success', __('Thank you for contacting us! We will get back to you soon.'));
    }

    public function contact()
    {
        $siteSettings = SiteSetting::all()->keyBy('key');
        return view('contact', compact('siteSettings'));
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Helpers\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteManagementController extends Controller
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

    public function index()
    {
        $this->checkAuth();
        $sites = Site::with(['projects' => function($query) {
            $query->orderBy('type')->orderBy('created_at', 'desc');
        }])->latest()->paginate(20);

        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        $this->checkAuth();
        return view('sites.create');
    }

    public function store(Request $request)
    {
        $this->checkAuth();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = UploadHelper::uploadFile('sites', $request->file('image'));
        }

        Site::create($validated);

        return redirect()->route('sites.index')->with('success', __('Site created successfully.'));
    }

    public function show(Site $site)
    {
        $this->checkAuth();
        $site->load(['projects.images']);
        return view('sites.show', compact('site'));
    }

    public function edit(Site $site)
    {
        $this->checkAuth();
        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $this->checkAuth();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            if ($site->image) {
                UploadHelper::deleteFile($site->image);
            }
            $validated['image'] = UploadHelper::uploadFile('sites', $request->file('image'));
        }

        $site->update($validated);

        return redirect()->route('sites.index')->with('success', __('Site updated successfully.'));
    }

    public function destroy(Site $site)
    {
        $this->checkAuth();
        $site->delete();
        return redirect()->route('sites.index')->with('success', __('Site deleted successfully.'));
    }

    // Project methods
    public function createProject(Site $site)
    {
        $this->checkAuth();
        return view('sites.projects.create', compact('site'));
    }

    public function storeProject(Request $request, Site $site)
    {
        $this->checkAuth();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'main_image' => 'nullable|image|max:10240',
            'layout_image' => 'nullable|image|max:10240',
            'type' => 'required|in:current,previous',
            'images' => 'nullable|array',
            'images.*' => 'image|max:10240',
        ]);

        $validated['site_id'] = $site->id;

        if ($request->hasFile('main_image')) {
            $validated['main_image'] = UploadHelper::uploadFile('projects', $request->file('main_image'));
        }

        if ($request->hasFile('layout_image')) {
            $validated['layout_image'] = UploadHelper::uploadFile('projects', $request->file('layout_image'));
        }

        $project = Project::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = UploadHelper::uploadFile('projects', $image);
                $project->images()->create([
                    'image_path' => $path,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('sites.show', $site)->with('success', __('Project created successfully.'));
    }

    public function editProject(Site $site, Project $project)
    {
        $this->checkAuth();
        if ($project->site_id !== $site->id) {
            abort(404);
        }
        $project->load('images');
        return view('sites.projects.edit', compact('site', 'project'));
    }

    public function updateProject(Request $request, Site $site, Project $project)
    {
        $this->checkAuth();
        if ($project->site_id !== $site->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'main_image' => 'nullable|image|max:10240',
            'layout_image' => 'nullable|image|max:10240',
            'type' => 'required|in:current,previous',
            'images' => 'nullable|array',
            'images.*' => 'image|max:10240',
        ]);

        if ($request->hasFile('main_image')) {
            if ($project->main_image) {
                UploadHelper::deleteFile($project->main_image);
            }
            $validated['main_image'] = UploadHelper::uploadFile('projects', $request->file('main_image'));
        }

        if ($request->hasFile('layout_image')) {
            if ($project->layout_image) {
                UploadHelper::deleteFile($project->layout_image);
            }
            $validated['layout_image'] = UploadHelper::uploadFile('projects', $request->file('layout_image'));
        }

        $project->update($validated);

        if ($request->hasFile('images')) {
            $maxOrder = $project->images()->max('order') ?? -1;
            foreach ($request->file('images') as $index => $image) {
                $path = UploadHelper::uploadFile('projects', $image);
                $project->images()->create([
                    'image_path' => $path,
                    'order' => $maxOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('sites.show', $site)->with('success', __('Project updated successfully.'));
    }

    public function destroyProject(Site $site, Project $project)
    {
        $this->checkAuth();
        if ($project->site_id !== $site->id) {
            abort(404);
        }

        $project->delete();
        return redirect()->route('sites.show', $site)->with('success', __('Project deleted successfully.'));
    }

    public function destroyProjectImage(ProjectImage $projectImage)
    {
        $this->checkAuth();
        $site = $projectImage->project->site;
        $projectImage->delete();
        return redirect()->route('sites.projects.edit', ['site' => $site, 'project' => $projectImage->project])
            ->with('success', __('Image deleted successfully.'));
    }
}


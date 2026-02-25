<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\Service;
use App\Models\HowItWorksStep;
use App\Helpers\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SiteController extends Controller
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
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->checkAdmin();
        return view('site.index');
    }

    public function partners()
    {
        $this->checkAdmin();
        $partners = Partner::orderBy('order')->get();
        return view('site.partners.index', compact('partners'));
    }

    public function partnersStore(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = UploadHelper::uploadFile('partners', $request->file('logo'));
        }

        Partner::create($validated);

        return redirect()->route('site.partners')->with('success', __('Partner created successfully.'));
    }

    public function partnersUpdate(Request $request, Partner $partner)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            if ($partner->logo) {
                UploadHelper::deleteFile($partner->logo);
            }
            $validated['logo'] = UploadHelper::uploadFile('partners', $request->file('logo'));
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        $partner->update($validated);

        return redirect()->route('site.partners')->with('success', __('Partner updated successfully.'));
    }

    public function partnersDestroy(Partner $partner)
    {
        $this->checkAdmin();
        if ($partner->logo) {
            UploadHelper::deleteFile($partner->logo);
        }
        $partner->delete();

        return redirect()->route('site.partners')->with('success', __('Partner deleted successfully.'));
    }

    public function content(Request $request)
    {
        $this->checkAdmin();

        $enPath = resource_path('lang/en.json');
        $arPath = resource_path('lang/ar.json');

        $enContent = File::exists($enPath) ? File::get($enPath) : '{}';
        $arContent = File::exists($arPath) ? File::get($arPath) : '{}';

        // Decode JSON to validate
        $enData = json_decode($enContent, true) ?: [];
        $arData = json_decode($arContent, true) ?: [];

        // Get locale from request or default to 'en'
        $locale = $request->get('locale', 'en');
        $currentData = $locale === 'ar' ? $arData : $enData;

        // Search filter
        $search = $request->get('search', '');

        // Convert array to collection for filtering
        $items = collect($currentData)->map(function ($value, $key) {
            // Convert value to string for searching
            $valueStr = is_array($value) ? json_encode($value) : (string)$value;
            return ['key' => (string)$key, 'value' => $valueStr];
        })->values();

        // Filter by search term if provided
        if (!empty($search)) {
            $searchLower = mb_strtolower(trim($search), 'UTF-8');
            $items = $items->filter(function ($item) use ($searchLower) {
                $keyLower = mb_strtolower($item['key'], 'UTF-8');
                $valueLower = mb_strtolower($item['value'], 'UTF-8');
                // Use mb_strpos for better UTF-8 support
                return mb_strpos($keyLower, $searchLower) !== false ||
                    mb_strpos($valueLower, $searchLower) !== false;
            })->values();
        }

        // Convert to array - no pagination
        $itemsArray = $items->all();

        return view('site.content.index', compact('enData', 'arData', 'enContent', 'arContent', 'itemsArray', 'locale', 'search'));
    }

    public function contentUpdate(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'locale' => 'required|in:en,ar',
            'content' => 'required|json',
        ]);

        $locale = $validated['locale'];
        $content = $validated['content'];

        // Validate JSON
        $decoded = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withErrors(['content' => 'Invalid JSON format: ' . json_last_error_msg()]);
        }

        // Format JSON with proper indentation
        $formatted = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $path = resource_path("lang/{$locale}.json");
        File::put($path, $formatted);

        return redirect()->route('site.content')->with('success', __('Content updated successfully.'));
    }

    public function hero()
    {
        $this->checkAdmin();
        $heroImage = SiteSetting::getValue('hero_image');
        return view('site.hero', compact('heroImage'));
    }

    public function heroUpdate(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'hero_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('hero_image')) {
            $oldImage = SiteSetting::getValue('hero_image');
            if ($oldImage) {
                UploadHelper::deleteFile($oldImage);
            }
            $path = UploadHelper::uploadFile('hero', $request->file('hero_image'));
            SiteSetting::setValue('hero_image', $path);
        }

        return redirect()->route('site.hero')->with('success', __('Hero image updated successfully.'));
    }

    public function heroDelete()
    {
        $this->checkAdmin();

        $heroImage = SiteSetting::getValue('hero_image');
        if ($heroImage) {
            UploadHelper::deleteFile($heroImage);
            SiteSetting::setValue('hero_image', null);
        }

        return redirect()->route('site.hero')->with('success', __('Hero image deleted successfully.'));
    }

    public function settings()
    {
        $this->checkAdmin();
        $settings = SiteSetting::all()->keyBy('key');
        return view('site.settings', compact('settings'));
    }

    public function settingsUpdate(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|max:2048',
            'site_favicon' => 'nullable|image|max:512',
            'site_address' => 'nullable|string|max:500',
            'site_address2' => 'nullable|string|max:500',
            'site_phone' => 'nullable|string|max:255',
            'site_phone2' => 'nullable|string|max:255',
            'site_email' => 'nullable|email|max:255',
            'site_facebook' => 'nullable|url|max:255',
            'site_twitter' => 'nullable|url|max:255',
            'site_instagram' => 'nullable|url|max:255',
            'site_linkedin' => 'nullable|url|max:255',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'site_logo' || $key === 'site_favicon') {
                if ($request->hasFile($key)) {
                    $oldValue = SiteSetting::getValue($key);
                    if ($oldValue) {
                        UploadHelper::deleteFile($oldValue);
                    }
                    $path = UploadHelper::uploadFile('site', $request->file($key));
                    SiteSetting::setValue($key, $path);
                }
            } else {
                SiteSetting::setValue($key, $value);
            }
        }

        return redirect()->route('site.settings')->with('success', __('Settings updated successfully.'));
    }

    public function testimonials()
    {
        $this->checkAdmin();
        $testimonials = Testimonial::orderBy('order')->get();
        return view('site.testimonials.index', compact('testimonials'));
    }

    public function testimonialsStore(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'testimonial' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'rating' => 'nullable|integer|min:1|max:5',
            'property_sold' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = UploadHelper::uploadFile('testimonials', $request->file('photo'));
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        if (!isset($validated['rating'])) {
            $validated['rating'] = 5;
        }

        Testimonial::create($validated);

        return redirect()->route('site.testimonials')->with('success', __('Testimonial created successfully.'));
    }

    public function testimonialsUpdate(Request $request, Testimonial $testimonial)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'testimonial' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'rating' => 'nullable|integer|min:1|max:5',
            'property_sold' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($testimonial->photo) {
                UploadHelper::deleteFile($testimonial->photo);
            }
            $validated['photo'] = UploadHelper::uploadFile('testimonials', $request->file('photo'));
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        $testimonial->update($validated);

        return redirect()->route('site.testimonials')->with('success', __('Testimonial updated successfully.'));
    }

    public function testimonialsDestroy(Testimonial $testimonial)
    {
        $this->checkAdmin();
        if ($testimonial->photo) {
            UploadHelper::deleteFile($testimonial->photo);
        }
        $testimonial->delete();

        return redirect()->route('site.testimonials')->with('success', __('Testimonial deleted successfully.'));
    }

    public function services()
    {
        $this->checkAdmin();
        $services = Service::orderBy('order')->get();
        return view('site.services.index', compact('services'));
    }

    public function servicesStore(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'icon_type' => 'required|in:emoji,image',
            'icon_image' => 'nullable|image|max:2048',
            'link' => 'nullable|url|max:255',
            'link_text' => 'nullable|string|max:255',
            'link_text_en' => 'nullable|string|max:255',
            'link_text_ar' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('icon_image')) {
            $validated['icon_image'] = UploadHelper::uploadFile('services', $request->file('icon_image'));
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        if (!isset($validated['icon_type'])) {
            $validated['icon_type'] = 'emoji';
        }

        Service::create($validated);

        return redirect()->route('site.services')->with('success', __('Service created successfully.'));
    }

    public function servicesUpdate(Request $request, Service $service)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'icon_type' => 'required|in:emoji,image',
            'icon_image' => 'nullable|image|max:2048',
            'link' => 'nullable|url|max:255',
            'link_text' => 'nullable|string|max:255',
            'link_text_en' => 'nullable|string|max:255',
            'link_text_ar' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('icon_image')) {
            if ($service->icon_image) {
                UploadHelper::deleteFile($service->icon_image);
            }
            $validated['icon_image'] = UploadHelper::uploadFile('services', $request->file('icon_image'));
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        $service->update($validated);

        return redirect()->route('site.services')->with('success', __('Service updated successfully.'));
    }

    public function servicesDestroy(Service $service)
    {
        $this->checkAdmin();
        if ($service->icon_image) {
            UploadHelper::deleteFile($service->icon_image);
        }
        $service->delete();

        return redirect()->route('site.services')->with('success', __('Service deleted successfully.'));
    }

    public function howItWorks()
    {
        $this->checkAdmin();
        $steps = HowItWorksStep::orderBy('order')->get();
        return view('site.how-it-works.index', compact('steps'));
    }

    public function howItWorksStore(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'icon_type' => 'required|in:emoji,image',
            'icon_image' => 'nullable|image|max:2048',
            'step_number' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('icon_image')) {
            $validated['icon_image'] = UploadHelper::uploadFile('how-it-works', $request->file('icon_image'));
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        if (!isset($validated['icon_type'])) {
            $validated['icon_type'] = 'emoji';
        }

        HowItWorksStep::create($validated);

        return redirect()->route('site.how-it-works')->with('success', __('Step created successfully.'));
    }

    public function howItWorksUpdate(Request $request, HowItWorksStep $howItWorksStep)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'icon_type' => 'required|in:emoji,image',
            'icon_image' => 'nullable|image|max:2048',
            'step_number' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('icon_image')) {
            if ($howItWorksStep->icon_image) {
                UploadHelper::deleteFile($howItWorksStep->icon_image);
            }
            $validated['icon_image'] = UploadHelper::uploadFile('how-it-works', $request->file('icon_image'));
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        $howItWorksStep->update($validated);

        return redirect()->route('site.how-it-works')->with('success', __('Step updated successfully.'));
    }

    public function howItWorksDestroy(HowItWorksStep $howItWorksStep)
    {
        $this->checkAdmin();
        if ($howItWorksStep->icon_image) {
            UploadHelper::deleteFile($howItWorksStep->icon_image);
        }
        $howItWorksStep->delete();

        return redirect()->route('site.how-it-works')->with('success', __('Step deleted successfully.'));
    }

    // About Page Management
    public function about()
    {
        $this->checkAdmin();
        $settings = SiteSetting::whereIn('key', [
            'about_hero_title_en',
            'about_hero_title_ar',
            'about_hero_subtitle_en',
            'about_hero_subtitle_ar',
            'about_story_title_en',
            'about_story_title_ar',
            'about_story_text_en',
            'about_story_text_ar',
            'about_mission_text_en',
            'about_mission_text_ar',
            'about_stats_years',
            'about_stats_homes',
            'about_stats_satisfaction',
            'about_stats_award',
            'about_image',
            'about_video',
            'about_value_trust_title_en',
            'about_value_trust_title_ar',
            'about_value_trust_text_en',
            'about_value_trust_text_ar',
            'about_value_excellence_title_en',
            'about_value_excellence_title_ar',
            'about_value_excellence_text_en',
            'about_value_excellence_text_ar',
            'about_value_innovation_title_en',
            'about_value_innovation_title_ar',
            'about_value_innovation_text_en',
            'about_value_innovation_text_ar',
        ])->get()->keyBy('key');

        return view('site.about.index', compact('settings'));
    }

    public function aboutUpdate(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'about_hero_title_en' => 'nullable|string|max:255',
            'about_hero_title_ar' => 'nullable|string|max:255',
            'about_hero_subtitle_en' => 'nullable|string|max:255',
            'about_hero_subtitle_ar' => 'nullable|string|max:255',
            'about_story_title_en' => 'nullable|string|max:255',
            'about_story_title_ar' => 'nullable|string|max:255',
            'about_story_text_en' => 'nullable|string',
            'about_story_text_ar' => 'nullable|string',
            'about_mission_text_en' => 'nullable|string',
            'about_mission_text_ar' => 'nullable|string',
            'about_stats_years' => 'nullable|string|max:50',
            'about_stats_homes' => 'nullable|string|max:50',
            'about_stats_satisfaction' => 'nullable|string|max:50',
            'about_stats_award' => 'nullable|string|max:255',
            'about_image' => 'nullable|image|max:5120',
            'about_video' => 'nullable|mimes:mp4,webm,ogg,mov|max:51200',
            'about_value_trust_title_en' => 'nullable|string|max:255',
            'about_value_trust_title_ar' => 'nullable|string|max:255',
            'about_value_trust_text_en' => 'nullable|string',
            'about_value_trust_text_ar' => 'nullable|string',
            'about_value_excellence_title_en' => 'nullable|string|max:255',
            'about_value_excellence_title_ar' => 'nullable|string|max:255',
            'about_value_excellence_text_en' => 'nullable|string',
            'about_value_excellence_text_ar' => 'nullable|string',
            'about_value_innovation_title_en' => 'nullable|string|max:255',
            'about_value_innovation_title_ar' => 'nullable|string|max:255',
            'about_value_innovation_text_en' => 'nullable|string',
            'about_value_innovation_text_ar' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'about_image' && $request->hasFile('about_image')) {
                $oldImage = SiteSetting::where('key', 'about_image')->first();
                if ($oldImage && $oldImage->value) {
                    UploadHelper::deleteFile($oldImage->value);
                }
                $value = UploadHelper::uploadFile('about', $request->file('about_image'));
                SiteSetting::updateOrCreate(
                    ['key' => 'about_image'],
                    ['value' => $value, 'type' => 'image']
                );
            } elseif ($key === 'about_video' && $request->hasFile('about_video')) {
                $oldVideo = SiteSetting::where('key', 'about_video')->first();
                if ($oldVideo && $oldVideo->value) {
                    UploadHelper::deleteFile($oldVideo->value);
                }
                $value = UploadHelper::uploadFile('about', $request->file('about_video'));
                SiteSetting::updateOrCreate(
                    ['key' => 'about_video'],
                    ['value' => $value, 'type' => 'video']
                );
            } elseif ($key !== 'about_image' && $key !== 'about_video') {
                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'type' => 'text']
                );
            }
        }

        return redirect()->route('site.about')->with('success', __('About page content updated successfully.'));
    }
}

<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerCommunicationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\LeadStageController;
use App\Http\Controllers\LeadTagController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SiteManagementController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppServiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect home to admin login
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/listings', [HomeController::class, 'listings'])->name('listings');
Route::get('/sites/{site}/projects', [HomeController::class, 'siteProjects'])->name('site.projects');
Route::get('/sites/{site}/projects/{project}', [HomeController::class, 'projectDetails'])->name('project.details');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/service/{service}', [HomeController::class, 'serviceDetails'])->name('service.details');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');

// Redirect authenticated users from home to dashboard
Route::get('/home', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('home');
});

// Language switching (must be outside auth middleware to work for all users)
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
        // Save to session immediately
        session()->save();
    }

    return redirect()->back();
})->name('language.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/lead-stats', [DashboardController::class, 'leadStats'])->name('dashboard.lead-stats');
    Route::get('/lead-stats/stage/{stage}', [DashboardController::class, 'leadsByStage'])->name('dashboard.leads-by-stage');
    Route::get('/missed-events', [DashboardController::class, 'missedEvents'])->name('dashboard.missed-events');
    Route::get('/calendar/missed-events', [CalendarController::class, 'missedEvents'])->name('calendar.missed-events');

    // Set route parameter patterns to only accept numeric IDs
    Route::pattern('lead', '[0-9]+');
    Route::pattern('customer', '[0-9]+');
    Route::pattern('appointment', '[0-9]+');
    Route::pattern('unit', '[0-9]+');
    Route::pattern('apartment', '[0-9]+');
    Route::pattern('user', '[0-9]+');
    Route::pattern('site', '[0-9]+');
    Route::pattern('project', '[0-9]+');
    Route::pattern('projectImage', '[0-9]+');

    // Leads
    Route::post('/leads/bulk-delete', [LeadController::class, 'bulkDestroy'])->name('leads.bulk-delete');

    // Update stage: السماح بكل من GET و POST لتفادي 405 من أي طلبات غير متوقعة
    Route::match(['GET', 'POST'], '/leads/{lead}/update-stage', [LeadController::class, 'updateStage'])
        ->name('leads.update-stage');

    // Activities: POST لإضافة activity، GET يعيد التوجيه لتفاصيل الليد
    Route::get('/leads/{lead}/activities', fn ($lead) => redirect()->route('leads.show', $lead))->name('leads.activities.index');
    Route::post('/leads/{lead}/activities', [LeadController::class, 'storeActivity'])->name('leads.activities.store');
    Route::post('/leads/{lead}/comments', [LeadController::class, 'storeComment'])->name('leads.comments.store');
    Route::post('/leads/{lead}/events', [LeadController::class, 'storeEvent'])->name('leads.events.store');
    Route::resource('leads', LeadController::class);

    // Contracts
    Route::resource('contracts', ContractController::class)->except(['create', 'store']);

    // Lead Stages & Tags
    Route::resource('lead-stages', LeadStageController::class)->except(['show']);
    Route::resource('lead-sources', LeadSourceController::class)->except(['show']);
    Route::resource('lead-tags', LeadTagController::class)->except(['show', 'edit', 'update']);

    // Customers
    Route::post('/customers/bulk-delete', [CustomerController::class, 'bulkDestroy'])->name('customers.bulk-delete');
    Route::resource('customers', CustomerController::class);

    // Customer Communications
    Route::resource('customer-communications', CustomerCommunicationController::class);

    // Appointments
    Route::post('/appointments/bulk-delete', [AppointmentController::class, 'bulkDestroy'])->name('appointments.bulk-delete');
    Route::resource('appointments', AppointmentController::class);

    // Units
    Route::post('/units/bulk-delete', [UnitController::class, 'bulkDestroy'])->name('units.bulk-delete');
    Route::post('/units/{unit}/reserve', [UnitController::class, 'reserve'])->name('units.reserve');
    Route::post('/units/{unit}/sell', [UnitController::class, 'sell'])->name('units.sell');
    Route::resource('units', UnitController::class);

    // Apartments (within Units)
    Route::prefix('units/{unit}')->name('units.apartments.')->group(function () {
        Route::get('/apartments', [ApartmentController::class, 'index'])->name('index');
        Route::get('/apartments/create', [ApartmentController::class, 'create'])->name('create');
        Route::post('/apartments', [ApartmentController::class, 'store'])->name('store');
        Route::get('/apartments/{apartment}', [ApartmentController::class, 'show'])->name('show');
        Route::get('/apartments/{apartment}/edit', [ApartmentController::class, 'edit'])->name('edit');
        Route::put('/apartments/{apartment}', [ApartmentController::class, 'update'])->name('update');
        Route::delete('/apartments/{apartment}', [ApartmentController::class, 'destroy'])->name('destroy');
    });

    // Sites Management
    Route::resource('sites', SiteManagementController::class);
    Route::get('/sites/{site}/projects/create', [SiteManagementController::class, 'createProject'])->name('sites.projects.create');
    Route::post('/sites/{site}/projects', [SiteManagementController::class, 'storeProject'])->name('sites.projects.store');
    Route::get('/sites/{site}/projects/{project}/edit', [SiteManagementController::class, 'editProject'])->name('sites.projects.edit');
    Route::put('/sites/{site}/projects/{project}', [SiteManagementController::class, 'updateProject'])->name('sites.projects.update');
    Route::delete('/sites/{site}/projects/{project}', [SiteManagementController::class, 'destroyProject'])->name('sites.projects.destroy');
    Route::delete('/project-images/{projectImage}', [SiteManagementController::class, 'destroyProjectImage'])->name('project-images.destroy');

    // Project Units (Booking Plan Grid)
    Route::prefix('projects/{project}')->name('projects.units.')->group(function () {
        Route::get('/units', [\App\Http\Controllers\ProjectUnitController::class, 'index'])->name('index');
        Route::get('/units/configure', [\App\Http\Controllers\ProjectUnitController::class, 'configure'])->name('configure');
        Route::post('/units/configure', [\App\Http\Controllers\ProjectUnitController::class, 'storeConfiguration'])->name('store-configuration');
        Route::get('/units/create', [\App\Http\Controllers\ProjectUnitController::class, 'create'])->name('create');
        Route::post('/units', [\App\Http\Controllers\ProjectUnitController::class, 'store'])->name('store');
        Route::get('/units/{unit}', [\App\Http\Controllers\ProjectUnitController::class, 'show'])->name('show');
        Route::get('/units/{unit}/edit', [\App\Http\Controllers\ProjectUnitController::class, 'edit'])->name('edit');
        Route::put('/units/{unit}', [\App\Http\Controllers\ProjectUnitController::class, 'update'])->name('update');
        Route::delete('/units/{unit}', [\App\Http\Controllers\ProjectUnitController::class, 'destroy'])->name('destroy');
        Route::post('/units/{unit}/status', [\App\Http\Controllers\ProjectUnitController::class, 'updateStatus'])->name('update-status');
    });

    // Teams
    Route::resource('teams', TeamController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // WhatsApp Services
    Route::get('/whatsapp/services', [WhatsAppServiceController::class, 'index'])->name('whatsapp.services.index');
    Route::get('/whatsapp/services/chats', [WhatsAppServiceController::class, 'apiChats'])->name('whatsapp.services.api.chats');
    Route::get('/whatsapp/services/messages', [WhatsAppServiceController::class, 'apiMessages'])->name('whatsapp.services.api.messages');
    Route::get('/whatsapp/services/media', [WhatsAppServiceController::class, 'proxyMessageMedia'])->name('whatsapp.services.media');
    Route::post('/whatsapp/services/send', [WhatsAppServiceController::class, 'send'])->name('whatsapp.services.send');
    Route::post('/whatsapp/services/send-chat', [WhatsAppServiceController::class, 'sendChatReply'])->name('whatsapp.services.send-chat');
    Route::post('/whatsapp/services/send-leads', [WhatsAppServiceController::class, 'sendToLeads'])->name('whatsapp.services.send-leads');

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Users Management (Admin only)
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulk-delete');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class)->where(['user' => '[0-9]+']);

    // Clear Cache (Admin only)
    Route::get('/clear-cache', function () {

        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');

        return redirect()->back()->with('success', __('Cache cleared successfully.'));
    })->name('clear-cache');

    // Site Management (Admin only)
    Route::prefix('site')->name('site.')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('index');
        Route::get('/partners', [SiteController::class, 'partners'])->name('partners');
        Route::post('/partners', [SiteController::class, 'partnersStore'])->name('partners.store');
        Route::put('/partners/{partner}', [SiteController::class, 'partnersUpdate'])->name('partners.update');
        Route::delete('/partners/{partner}', [SiteController::class, 'partnersDestroy'])->name('partners.destroy');
        Route::get('/content', [SiteController::class, 'content'])->name('content');
        Route::post('/content', [SiteController::class, 'contentUpdate'])->name('content.update');
        Route::get('/hero', [SiteController::class, 'hero'])->name('hero');
        Route::post('/hero', [SiteController::class, 'heroUpdate'])->name('hero.update');
        Route::delete('/hero', [SiteController::class, 'heroDelete'])->name('hero.delete');
        Route::get('/settings', [SiteController::class, 'settings'])->name('settings');
        Route::post('/settings', [SiteController::class, 'settingsUpdate'])->name('settings.update');
        Route::get('/about', [SiteController::class, 'about'])->name('about');
        Route::post('/about', [SiteController::class, 'aboutUpdate'])->name('about.update');
        Route::get('/testimonials', [SiteController::class, 'testimonials'])->name('testimonials');
        Route::get('/testimonials/create', function () {
            return view('site.testimonials.create');
        })->name('testimonials.create');
        Route::post('/testimonials', [SiteController::class, 'testimonialsStore'])->name('testimonials.store');
        Route::get('/testimonials/{testimonial}/edit', function (\App\Models\Testimonial $testimonial) {
            return view('site.testimonials.edit', compact('testimonial'));
        })->name('testimonials.edit');
        Route::put('/testimonials/{testimonial}', [SiteController::class, 'testimonialsUpdate'])->name('testimonials.update');
        Route::delete('/testimonials/{testimonial}', [SiteController::class, 'testimonialsDestroy'])->name('testimonials.destroy');
        Route::get('/services', [SiteController::class, 'services'])->name('services');
        Route::get('/services/create', function () {
            return view('site.services.create');
        })->name('services.create');
        Route::post('/services', [SiteController::class, 'servicesStore'])->name('services.store');
        Route::get('/services/{service}/edit', function (\App\Models\Service $service) {
            return view('site.services.edit', compact('service'));
        })->name('services.edit');
        Route::put('/services/{service}', [SiteController::class, 'servicesUpdate'])->name('services.update');
        Route::delete('/services/{service}', [SiteController::class, 'servicesDestroy'])->name('services.destroy');
        Route::get('/how-it-works', [SiteController::class, 'howItWorks'])->name('how-it-works');
        Route::get('/how-it-works/create', function () {
            return view('site.how-it-works.create');
        })->name('how-it-works.create');
        Route::post('/how-it-works', [SiteController::class, 'howItWorksStore'])->name('how-it-works.store');
        Route::get('/how-it-works/{howItWorksStep}/edit', function (\App\Models\HowItWorksStep $howItWorksStep) {
            return view('site.how-it-works.edit', compact('howItWorksStep'));
        })->name('how-it-works.edit');
        Route::put('/how-it-works/{howItWorksStep}', [SiteController::class, 'howItWorksUpdate'])->name('how-it-works.update');
        Route::delete('/how-it-works/{howItWorksStep}', [SiteController::class, 'howItWorksDestroy'])->name('how-it-works.destroy');
    });

    // Export
    Route::get('/export/leads', [ExportController::class, 'leads'])->name('export.leads');
    Route::get('/export/customers', [ExportController::class, 'customers'])->name('export.customers');
    Route::get('/export/units', [ExportController::class, 'units'])->name('export.units');
    Route::get('/export/appointments', [ExportController::class, 'appointments'])->name('export.appointments');
    Route::get('/export/reports', [ExportController::class, 'reports'])->name('export.reports');
    Route::get('/export/users/{user}', [ExportController::class, 'userData'])->name('export.user');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/api/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

require __DIR__.'/auth.php';

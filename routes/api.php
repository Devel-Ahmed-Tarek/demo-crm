<?php

use App\Http\Controllers\Api\LeadApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api'])->prefix('v1')->group(function () {
    Route::post('/leads', [LeadApiController::class, 'store']);
    Route::get('/lead-metadata', [LeadApiController::class, 'metadata']);
});

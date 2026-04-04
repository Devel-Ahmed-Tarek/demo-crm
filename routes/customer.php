<?php

use App\Http\Controllers\Customer\MessagesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/messages', [MessagesController::class, 'index'])->name('messages');
    Route::post('/messages/send', [MessagesController::class, 'send'])->name('messages.send');
    Route::post('/messages/send-media', [MessagesController::class, 'sendMedia'])->name('messages.send-media');
    Route::get('/messages/poll', [MessagesController::class, 'poll'])->name('messages.poll');
    Route::get('/messages/load-more', [MessagesController::class, 'loadMore'])->name('messages.load-more');
    Route::get('/messages/media', [MessagesController::class, 'media'])->name('messages.media');
    Route::post('/messages/mark-seen', [MessagesController::class, 'markSeen'])->name('messages.mark-seen');
});

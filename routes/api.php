<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::post('/notification/receive', [NotificationController::class, 'receive'])
        ->middleware('decrypt.json')
        ->name('api.notification.receive');
});

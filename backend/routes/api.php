<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\CabinetSlotController;
use App\Http\Controllers\CollateralController;
use App\Http\Controllers\CollateralTypeController;
use App\Http\Controllers\ProductCollateralController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Tooling Room
|--------------------------------------------------------------------------
| Prefix: /api
| All responses are JSON.
*/

// ──────────────────────────────────────────────────────────────
// PUBLIC — Auth (không cần token)
// ──────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ──────────────────────────────────────────────────────────────
// PROTECTED — Toàn bộ API cần Bearer token
// ──────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth — user actions
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });

    // Collateral Types
    Route::prefix('collateral-types')->group(function () {
        Route::get('/',        [CollateralTypeController::class, 'index']);
        Route::post('/',       [CollateralTypeController::class, 'store']);
        Route::get('/{id}',    [CollateralTypeController::class, 'show']);
        Route::put('/{id}',    [CollateralTypeController::class, 'update']);
        Route::delete('/{id}', [CollateralTypeController::class, 'destroy']);
    });

    // Cabinets + nested Slots
    Route::prefix('cabinets')->group(function () {
        Route::get('/',        [CabinetController::class, 'index']);
        Route::post('/',       [CabinetController::class, 'store']);
        Route::get('/{id}',    [CabinetController::class, 'show']);
        Route::put('/{id}',    [CabinetController::class, 'update']);
        Route::delete('/{id}', [CabinetController::class, 'destroy']);

        Route::get('/{cabinetId}/slots',  [CabinetSlotController::class, 'index']);
        Route::post('/{cabinetId}/slots', [CabinetSlotController::class, 'store']);
    });

    // Standalone slot routes
    Route::prefix('slots')->group(function () {
        Route::get('/{slotId}',    [CabinetSlotController::class, 'show']);
        Route::put('/{slotId}',    [CabinetSlotController::class, 'update']);
        Route::delete('/{slotId}', [CabinetSlotController::class, 'destroy']);
    });

    // Products + product-collateral relationship
    Route::prefix('products')->group(function () {
        Route::get('/',        [ProductController::class, 'index']);
        Route::post('/',       [ProductController::class, 'store']);
        Route::get('/{id}',    [ProductController::class, 'show']);
        Route::put('/{id}',    [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);

        Route::get('/{id}/collaterals',                          [ProductController::class, 'collaterals']);
        Route::post('/{productId}/collaterals',                  [ProductCollateralController::class, 'attach']);
        Route::delete('/{productId}/collaterals/{collateralId}', [ProductCollateralController::class, 'detach']);
    });

    // Collaterals
    Route::prefix('collaterals')->group(function () {
        Route::get('/',        [CollateralController::class, 'index']);
        Route::post('/',       [CollateralController::class, 'store']);
        Route::get('/{id}',    [CollateralController::class, 'show']);
        Route::put('/{id}',    [CollateralController::class, 'update']);
        Route::delete('/{id}', [CollateralController::class, 'destroy']);

        Route::post('/{id}/assign-slot', [CollateralController::class, 'assignSlot']);
    });

    // Tickets + nested Items
    Route::prefix('tickets')->group(function () {
        Route::get('/',        [TicketController::class, 'index']);
        Route::post('/',       [TicketController::class, 'store']);
        Route::get('/{id}',    [TicketController::class, 'show']);
        Route::put('/{id}',    [TicketController::class, 'update']);
        Route::delete('/{id}', [TicketController::class, 'destroy']);

        Route::get('/{ticketId}/items',  [TicketItemController::class, 'index']);
        Route::post('/{ticketId}/items', [TicketItemController::class, 'store']);
    });

    // Standalone ticket-item routes
    Route::prefix('ticket-items')->group(function () {
        Route::get('/{itemId}',    [TicketItemController::class, 'show']);
        Route::put('/{itemId}',    [TicketItemController::class, 'update']);
        Route::delete('/{itemId}', [TicketItemController::class, 'destroy']);
    });

}); // end auth:sanctum

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|──────────────────────────────────────────────────────────────────────────────
| Routes publiques
|──────────────────────────────────────────────────────────────────────────────
*/
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// Services : publics (visibles sans connexion)
Route::get('categories', [ServiceController::class, 'categories']);
Route::get('services',       [ServiceController::class, 'index']);
Route::get('services/{service}', [ServiceController::class, 'show']);

// Callbacks paiement (webhooks depuis les gateways)
Route::post('payments/callback/{method}', [PaymentController::class, 'callback']);

/*
|──────────────────────────────────────────────────────────────────────────────
| Routes protégées (authentification Sanctum requise)
|──────────────────────────────────────────────────────────────────────────────
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Commandes
    Route::get('orders',           [OrderController::class, 'index']);
    Route::post('orders',          [OrderController::class, 'store']);
    Route::post('orders/mass',     [OrderController::class, 'massStore']);
    Route::get('orders/{order}',   [OrderController::class, 'show']);

    // Paiements
    Route::get('payments',         [PaymentController::class, 'index']);
    Route::post('payments',        [PaymentController::class, 'store']);
});

/*
|──────────────────────────────────────────────────────────────────────────────
| API publique pour revendeurs (clé API dans le header)
|──────────────────────────────────────────────────────────────────────────────
*/
Route::prefix('v2')->middleware('api.key')->group(function () {
    Route::post('/', [\App\Http\Controllers\Api\PublicApiController::class, 'handle']);
});
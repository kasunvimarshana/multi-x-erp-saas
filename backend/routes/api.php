<?php

use App\Http\Controllers\AuthController;
use App\Modules\Inventory\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// API v1 Routes
Route::prefix('v1')->group(function () {
    
    // Public routes
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'Multi-X ERP API is running',
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String(),
        ]);
    });
    
    // Authentication routes (public)
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Authentication routes (protected)
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('user', [AuthController::class, 'user']);
        });
        
        // Inventory Module Routes
        Route::prefix('inventory')->group(function () {
            
            // Products
            Route::get('products/search', [ProductController::class, 'search']);
            Route::get('products/below-reorder-level', [ProductController::class, 'belowReorderLevel']);
            Route::get('products/{id}/stock-history', [ProductController::class, 'stockHistory']);
            Route::apiResource('products', ProductController::class);
            
            // Stock Movements (to be implemented)
            // Route::apiResource('stock-movements', StockMovementController::class)->only(['index', 'store', 'show']);
            
        });
        
        // IAM Module Routes (to be implemented)
        // Route::prefix('iam')->group(function () {
        //     Route::apiResource('users', UserController::class);
        //     Route::apiResource('roles', RoleController::class);
        //     Route::apiResource('permissions', PermissionController::class);
        // });
        
    });
});


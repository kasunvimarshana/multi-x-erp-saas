<?php

use App\Http\Controllers\AuthController;
use App\Modules\CRM\Http\Controllers\CustomerController;
use App\Modules\IAM\Http\Controllers\PermissionController;
use App\Modules\IAM\Http\Controllers\RoleController;
use App\Modules\IAM\Http\Controllers\UserController;
use App\Modules\Inventory\Http\Controllers\ProductController;
use App\Modules\Procurement\Http\Controllers\PurchaseOrderController;
use App\Modules\Procurement\Http\Controllers\SupplierController;
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
        
        // CRM Module Routes
        Route::prefix('crm')->group(function () {
            Route::get('customers/search', [CustomerController::class, 'search']);
            Route::apiResource('customers', CustomerController::class);
        });
        
        // Procurement Module Routes
        Route::prefix('procurement')->group(function () {
            
            // Suppliers
            Route::get('suppliers/search', [SupplierController::class, 'search']);
            Route::get('suppliers/active', [SupplierController::class, 'active']);
            Route::apiResource('suppliers', SupplierController::class);
            
            // Purchase Orders
            Route::get('purchase-orders/search', [PurchaseOrderController::class, 'search']);
            Route::get('purchase-orders/pending', [PurchaseOrderController::class, 'pending']);
            Route::post('purchase-orders/{id}/approve', [PurchaseOrderController::class, 'approve']);
            Route::post('purchase-orders/{id}/receive', [PurchaseOrderController::class, 'receive']);
            Route::post('purchase-orders/{id}/cancel', [PurchaseOrderController::class, 'cancel']);
            Route::apiResource('purchase-orders', PurchaseOrderController::class);
            
        });
        
        // IAM Module Routes
        Route::prefix('iam')->group(function () {
            
            // Users
            Route::get('users/search', [UserController::class, 'search']);
            Route::get('users/active', [UserController::class, 'active']);
            Route::post('users/{id}/assign-roles', [UserController::class, 'assignRoles']);
            Route::post('users/{id}/sync-roles', [UserController::class, 'syncRoles']);
            Route::get('users/{id}/roles', [UserController::class, 'getRoles']);
            Route::get('users/{id}/permissions', [UserController::class, 'getPermissions']);
            Route::apiResource('users', UserController::class);
            
            // Roles
            Route::get('roles/system', [RoleController::class, 'systemRoles']);
            Route::get('roles/custom', [RoleController::class, 'customRoles']);
            Route::post('roles/{id}/assign-permissions', [RoleController::class, 'assignPermissions']);
            Route::post('roles/{id}/sync-permissions', [RoleController::class, 'syncPermissions']);
            Route::get('roles/{id}/permissions', [RoleController::class, 'getPermissions']);
            Route::get('roles/{id}/users', [RoleController::class, 'getUsers']);
            Route::apiResource('roles', RoleController::class);
            
            // Permissions (read-only)
            Route::get('permissions/grouped', [PermissionController::class, 'grouped']);
            Route::get('permissions/{id}/roles', [PermissionController::class, 'getRoles']);
            Route::get('permissions/{id}', [PermissionController::class, 'show']);
            Route::get('permissions', [PermissionController::class, 'index']);
            
        });
        
    });
});


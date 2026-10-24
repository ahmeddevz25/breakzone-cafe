<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CafeSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComboProductController;
use App\Http\Controllers\Admin\FoodController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\PurchaseReturnController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes (Optimized & Grouped)
|--------------------------------------------------------------------------
|
| Application routes optimized for Breakzone Cafe.
| Fully cacheable with zero closures.
|
*/

// Admin Authentication & Protected Area
Route::middleware(['admin.redirect'])->group(function () {
    // Direct Redirects to /admin/login
    Route::redirect('/', '/admin/login');
    Route::redirect('admin', '/admin/login');
    Route::redirect('login', '/admin/login');

    // Guest Auth Routes
    Route::controller(AdminController::class)->group(function () {
        Route::get('admin/login', 'LoginForm')->name('login');
        Route::post('admin/login', 'login')->name('login.submit');
    });

    // Authenticated Admin Routes
    Route::middleware(['auth'])->group(function () {

        // Dashboard, Logout & System Utilities
        Route::controller(AdminController::class)->group(function () {
            Route::get('admin/dashboard', 'index')->name('dashboard');
            Route::post('logout', 'logout')->name('logout');
            Route::get('clear-cache', 'clearcache')->name('clearcache');
            Route::get('site-down', 'siteDown')->name('site.down');
            Route::get('site-up', 'siteUp')->name('site.up');
        });

        // Users Management
        Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::match(['get', 'delete'], '/delete/{id}', 'destroy')->name('destroy');
        });

        // Roles Management (Supports both 'roles' and 'roles.index')
        Route::prefix('roles')->controller(RoleController::class)->group(function () {
            Route::get('/', 'index')->name('roles');
            Route::get('/list', 'index')->name('roles.index');
            Route::get('/create', 'create')->name('roles.create');
            Route::post('/store', 'store')->name('roles.store');
            Route::get('/{id}/edit', 'edit')->name('roles.edit');
            Route::post('/{id}/update', 'update')->name('roles.update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('roles.delete');
        });

        // Permissions Management (Supports both 'permissions' and 'permissions.index')
        Route::prefix('permissions')->controller(PermissionController::class)->group(function () {
            Route::get('/', 'index')->name('permissions');
            Route::get('/list', 'index')->name('permissions.index');
            Route::post('/store', 'store')->name('permissions.store');
            Route::get('/edit/{id}', 'edit')->name('permissions.edit');
            Route::post('/update/{id}', 'update')->name('permissions.update');
            Route::match(['get', 'delete'], '/delete/{id}', 'destroy')->name('permissions.delete');
        });

        // Stores Management
        Route::prefix('stores')->name('stores.')->controller(StoreController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Suppliers Management
        Route::prefix('suppliers')->name('suppliers.')->controller(SupplierController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Brands Management
        Route::prefix('brands')->name('brands.')->controller(BrandController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Categories Management
        Route::prefix('categories')->name('categories.')->controller(CategoryController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Units Management
        Route::prefix('units')->name('units.')->controller(UnitController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Items Management (Retail Products)
        Route::prefix('items')->name('items.')->controller(ItemController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Ingredients Management (Raw Materials)
        Route::prefix('ingredients')->name('ingredients.')->controller(IngredientController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Foods Management (Prepared Recipes)
        Route::prefix('foods')->name('foods.')->controller(FoodController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/generate-code', 'generateCode')->name('generate-code');
            Route::get('/{id}/edit-data', 'getFoodDetails')->name('edit-data');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Combo Products (Deals / Packages) Management
        Route::prefix('combo-products')->name('combo-products.')->controller(ComboProductController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/generate-code', 'generateCode')->name('generate-code');
            Route::get('/{id}/details', 'getComboDetails')->name('details');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Purchases & Inventory Stock-In Management
        Route::prefix('purchases')->name('purchases.')->controller(PurchaseController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/generate-po', 'generatePoNo')->name('generate-po');
            Route::get('/store-data', 'getStoreData')->name('store-data');
            Route::get('/product-info', 'getProductInfo')->name('product-info');
            Route::get('/{id}/edit-data', 'editData')->name('edit-data');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Purchase Returns & Inventory Stock-Out Management
        Route::prefix('purchase-returns')->name('purchase-returns.')->controller(PurchaseReturnController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/purchases', 'getPurchases')->name('purchases');
            Route::get('/purchase-items/{id}', 'getPurchaseItems')->name('purchase-items');
            Route::get('/{id}/show', 'show')->name('show');
            Route::post('/store', 'store')->name('store');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Cafe Settings
        Route::prefix('cafe-settings')->name('cafe-settings.')->controller(CafeSettingController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'update')->name('update');
        });

    });
});

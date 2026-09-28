<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CafeSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FoodController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Public & Maintenance Routes
Route::get('/', function () {
    return view('admin.login');
});

Route::get('/site-down', function () {
    Artisan::call('down');
    return 'Site is now under maintenance mode (down).';
});

Route::get('/site-up', function () {
    Artisan::call('up');
    return 'Site is now live (up).';
});

// Admin Authentication & Protected Area
Route::middleware(['admin.redirect'])->group(function () {
    Route::get('admin', function () {
        return redirect()->route('login');
    });

    // Guest Auth Routes
    Route::controller(AdminController::class)->group(function () {
        Route::get('admin/login', 'LoginForm')->name('login');
        Route::post('admin/login', 'login')->name('login.submit');
    });

    // Authenticated Admin Routes
    Route::middleware(['auth'])->group(function () {
        // Dashboard, Logout & Utilities
        Route::controller(AdminController::class)->group(function () {
            Route::get('admin/dashboard', 'index')->name('dashboard');
            Route::post('/logout', 'logout')->name('logout');
            Route::get('clear-cache', 'clearcache')->name('clearcache');
        });

        // Users Management
        Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::match(['get', 'delete'], '/delete/{id}', 'destroy')->name('destroy');
        });

        // Roles Management
        Route::prefix('roles')->controller(RoleController::class)->group(function () {
            Route::get('/', 'index')->name('roles');
            Route::get('/create', 'create')->name('roles.create');
            Route::post('/store', 'store')->name('roles.store');
            Route::get('/{id}/edit', 'edit')->name('roles.edit');
            Route::post('/{id}/update', 'update')->name('roles.update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('roles.delete');
        });

        // Permissions Management
        Route::prefix('permissions')->controller(PermissionController::class)->group(function () {
            Route::get('/', 'index')->name('permissions');
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

        // Items Management
        Route::prefix('items')->name('items.')->controller(ItemController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Ingredients Management
        Route::prefix('ingredients')->name('ingredients.')->controller(IngredientController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        
        // Foods Management
        Route::prefix('foods')->name('foods.')->controller(FoodController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/generate-code', 'generateCode')->name('generate-code');
            Route::get('/{id}/edit-data', 'getFoodDetails')->name('edit-data');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Purchases Management
        Route::prefix('purchases')->name('purchases.')->controller(PurchaseController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/generate-po', 'generatePoNo')->name('generate-po');
            Route::get('/product-info', 'getProductInfo')->name('product-info');
            Route::get('/{id}/edit-data', 'editData')->name('edit-data');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::match(['get', 'delete'], '/{id}/delete', 'destroy')->name('delete');
        });

        // Cafe Settings
        Route::prefix('cafe-settings')->name('cafe-settings.')->controller(CafeSettingController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'update')->name('update');
        });

        
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Support\Facades\Artisan;

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


Route::middleware(['admin.redirect'])->group(function () {
    Route::get('admin', function () {
        return redirect()->route('login');
    });

    Route::get('admin/login', [AdminController::class, 'LoginForm'])->name('login');
    Route::post('admin/login', [AdminController::class, 'login'])->name('login.submit');

    Route::middleware(['auth'])->group(function () {
        Route::get('admin/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        // Index - All Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::get('/users/delete/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        //Roles Management
        Route::get('/roles', [RoleController::class, 'index'])->name('roles');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::post('/roles/{id}/update', [RoleController::class, 'update'])->name('roles.update');
        Route::get('/roles/{id}/delete', [RoleController::class, 'destroy'])->name('roles.delete');

        //Permissions Management
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions');
        Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/edit/{id}', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::post('/permissions/update/{id}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::get('/permissions/delete/{id}', [PermissionController::class, 'destroy'])->name('permissions.delete');

        // Store Management
        Route::get('/stores', [\App\Http\Controllers\Admin\StoreController::class, 'index'])->name('stores.index');
        Route::post('/stores/store', [\App\Http\Controllers\Admin\StoreController::class, 'store'])->name('stores.store');
        Route::post('/stores/{id}/update', [\App\Http\Controllers\Admin\StoreController::class, 'update'])->name('stores.update');
        Route::get('/stores/{id}/delete', [\App\Http\Controllers\Admin\StoreController::class, 'destroy'])->name('stores.delete');

        // Suppliers Management
        Route::get('/suppliers', [\App\Http\Controllers\Admin\SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers/store', [\App\Http\Controllers\Admin\SupplierController::class, 'store'])->name('suppliers.store');
        Route::post('/suppliers/{id}/update', [\App\Http\Controllers\Admin\SupplierController::class, 'update'])->name('suppliers.update');
        Route::get('/suppliers/{id}/delete', [\App\Http\Controllers\Admin\SupplierController::class, 'destroy'])->name('suppliers.delete');

        // Brands Management
        Route::get('/brands', [\App\Http\Controllers\Admin\BrandController::class, 'index'])->name('brands.index');
        Route::post('/brands/store', [\App\Http\Controllers\Admin\BrandController::class, 'store'])->name('brands.store');
        Route::post('/brands/{id}/update', [\App\Http\Controllers\Admin\BrandController::class, 'update'])->name('brands.update');
        Route::get('/brands/{id}/delete', [\App\Http\Controllers\Admin\BrandController::class, 'destroy'])->name('brands.delete');

        // Categories Management
        Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories/store', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/{id}/update', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::get('/categories/{id}/delete', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.delete');

        // Units Management
        Route::get('/units', [\App\Http\Controllers\Admin\UnitController::class, 'index'])->name('units.index');
        Route::post('/units/store', [\App\Http\Controllers\Admin\UnitController::class, 'store'])->name('units.store');
        Route::post('/units/{id}/update', [\App\Http\Controllers\Admin\UnitController::class, 'update'])->name('units.update');
        Route::get('/units/{id}/delete', [\App\Http\Controllers\Admin\UnitController::class, 'destroy'])->name('units.delete');

        // Items Management
        Route::get('/items', [\App\Http\Controllers\Admin\ItemController::class, 'index'])->name('items.index');
        Route::post('/items/store', [\App\Http\Controllers\Admin\ItemController::class, 'store'])->name('items.store');
        Route::post('/items/{id}/update', [\App\Http\Controllers\Admin\ItemController::class, 'update'])->name('items.update');
        Route::get('/items/{id}/delete', [\App\Http\Controllers\Admin\ItemController::class, 'destroy'])->name('items.delete');

        // Ingredients Management
        Route::get('/ingredients', [\App\Http\Controllers\Admin\IngredientController::class, 'index'])->name('ingredients.index');
        Route::post('/ingredients/store', [\App\Http\Controllers\Admin\IngredientController::class, 'store'])->name('ingredients.store');
        Route::post('/ingredients/{id}/update', [\App\Http\Controllers\Admin\IngredientController::class, 'update'])->name('ingredients.update');
        Route::get('/ingredients/{id}/delete', [\App\Http\Controllers\Admin\IngredientController::class, 'destroy'])->name('ingredients.delete');

        // Foods Management
        Route::get('/foods', [\App\Http\Controllers\Admin\FoodController::class, 'index'])->name('foods.index');
        Route::post('/foods/store', [\App\Http\Controllers\Admin\FoodController::class, 'store'])->name('foods.store');
        Route::post('/foods/{id}/update', [\App\Http\Controllers\Admin\FoodController::class, 'update'])->name('foods.update');
        Route::get('/foods/{id}/delete', [\App\Http\Controllers\Admin\FoodController::class, 'destroy'])->name('foods.delete');
        Route::get('/foods/generate-code', [\App\Http\Controllers\Admin\FoodController::class, 'generateCode'])->name('foods.generate-code');
        Route::get('/foods/{id}/edit-data', [\App\Http\Controllers\Admin\FoodController::class, 'getFoodDetails'])->name('foods.edit-data');

        // Purchases Management
        Route::get('/purchases', [\App\Http\Controllers\Admin\PurchaseController::class, 'index'])->name('purchases.index');
        Route::post('/purchases/store', [\App\Http\Controllers\Admin\PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{id}/edit-data', [\App\Http\Controllers\Admin\PurchaseController::class, 'editData'])->name('purchases.edit-data');
        Route::post('/purchases/{id}/update', [\App\Http\Controllers\Admin\PurchaseController::class, 'update'])->name('purchases.update');
        Route::get('/purchases/{id}/delete', [\App\Http\Controllers\Admin\PurchaseController::class, 'destroy'])->name('purchases.delete');
        Route::get('/purchases/generate-po', [\App\Http\Controllers\Admin\PurchaseController::class, 'generatePoNo'])->name('purchases.generate-po');
        Route::get('/purchases/product-info', [\App\Http\Controllers\Admin\PurchaseController::class, 'getProductInfo'])->name('purchases.product-info');

        Route::get('clear-cache', [AdminController::class, 'clearcache'])->name('clearcache');

        // Cafe Settings Routes
        Route::controller(App\Http\Controllers\Admin\CafeSettingController::class)->group(function () {
            Route::get('cafe-settings', 'index')->name('cafe-settings.index');
            Route::post('cafe-settings', 'update')->name('cafe-settings.update');
        });
    });
});

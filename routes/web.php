<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('about-us', 'users.about')->name('about');
Route::view('shipping-policy', 'users.shipping_policy')->name('shipping_policy');
Route::view('return-exchange-policy', 'users.return_policy')->name('return_policy');
Route::view('customer-service', 'users.customer_service')->name('customer_service');
Route::view('contact-us', 'users.contact')->name('contact');
Route::view('videos', 'users.videos')->name('videos');
Route::view('catalogue', 'users.catalogue')->name('catalogue');

Route::controller(PageController::class)->group(function () {
    Route::get('products/{slug?}', 'listing')->name('listing');
    Route::post('enquiry-store', 'storeEnquiry')->name('storeEnquiry');
    Route::get('test-mail', 'testMail')->name('testMail');
    Route::get('collections/', 'categories')->name('categories');
});

Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'view')->name('view');
    Route::post('/add', 'add')->name('add');
    Route::post('/update', 'update')->name('update');
    Route::post('/remove', 'remove')->name('remove');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/order-confirmation/{order}', [CheckoutController::class, 'confirmation'])
        ->name('order.confirmation');
});

require __DIR__.'/auth.php';

Route::namespace('App\Http\Controllers\Admin')->middleware(['auth', 'admin'])->prefix('admin')->name('admin.')
    ->group(function () {

        Route::controller(ProductsController::class)->group(function () {
            Route::get('products/trash', 'trash')->name('products.trash');
            Route::post('products/{id}/restore', 'restore')->name('products.restore');
            Route::delete('products/{id}/force-delete', 'forceDelete')->name('products.forceDelete');
        });

        Route::controller(ProductImageController::class)->group(function () {
            Route::post('/admin/products/{product}/upload-single-image', 'storeSingle')->name('products.images.store');
            Route::delete('/admin/products/{product}/images/{image}', 'destroy')->name('products.images.destroy');
        });

        Route::controller(HomePageController::class)->group(function () {
            Route::get('/home-settings', 'index')->name('settings.index');
            Route::get('/home-settings/edit', 'edit')->name('settings.edit');
            Route::put('/home-settings/update', 'update')->name('settings.update');
        });

        Route::controller(ProductAttributeController::class)->prefix('products/{product}/attributes')->name('products.attributes.')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::put('/{attribute}', 'update')->name('update');
            Route::delete('/{attribute}', 'destroy')->name('destroy');
        });

        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductsController::class);
        Route::resource('users', UserController::class);
        Route::resource('shippingcharges', ShippingChargesController::class);
        Route::resource('orders', OrderController::class);
        Route::resource('videos', VideoController::class);
        Route::resource('enquiries', EnquiryController::class);
    });

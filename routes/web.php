<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page routes
Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('/');

    Route::get('/about', 'about')->name('about');

    Route::get('list', 'propertyList')->name('property.list');
    Route::get('type', 'propertyType')->name('property.type');
    Route::get('agent', 'propertyAgent')->name('property.agent');

    Route::get('/contact', 'contact')->name('contact');
    Route::get('/testimonial', 'testimonial')->name('testimonial');
    Route::get('/404', 'notFound')->name('404');
    Route::get('allproperties', 'allproperties')->name('allproperties');

});

Route::middleware(['auth','check.client'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('properties', App\Http\Controllers\PropertyController::class);
    Route::resource('property-images', App\Http\Controllers\PropertyImageController::class);
    Route::resource('amenities', App\Http\Controllers\AmenityController::class);
    Route::resource('property-amenities', App\Http\Controllers\PropertyAmenityController::class);
    Route::resource('reservations', App\Http\Controllers\ReservationController::class);
    Route::resource('payments', App\Http\Controllers\PaymentController::class);
    Route::resource('reviews', App\Http\Controllers\ReviewController::class);
    Route::resource('messages', App\Http\Controllers\MessageController::class);
    Route::resource('depenses', App\Http\Controllers\DepenseController::class);
    Route::resource('services', App\Http\Controllers\ServiceController::class);
    // Routes d'archivage
    Route::get('archived', [App\Http\Controllers\MessageController::class, 'archived'])->name('archived');
    Route::post('messages/{message}/archive', [App\Http\Controllers\MessageController::class, 'archive'])->name('messages.archive');
    Route::post('messages/{message}/unarchive', [App\Http\Controllers\MessageController::class, 'unarchive'])->name('messages.unarchive');

    Route::resource('notifications', App\Http\Controllers\NotificationController::class);
    Route::resource('maintenances', App\Http\Controllers\MaintenanceController::class);
    Route::resource('documents', App\Http\Controllers\DocumentController::class);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Routes pour la gestion des images
    Route::delete('/property-images/{image}', [PropertyController::class, 'deleteImage'])
    ->name('properties.delete-image');
    Route::post('/property-images/{image}/primary', [PropertyController::class, 'setPrimaryImage'])
    ->name('properties.set-primary-image');

    // Routes additionnelles pour la gestion des images
    Route::post('properties/{property}/images', [PropertyController::class, 'uploadImages'])
        ->name('properties.upload-images');
    Route::delete('properties/images/{image}', [PropertyController::class, 'deleteImage'])
        ->name('properties.delete-image');
    Route::post('properties/images/{image}/set-primary', [PropertyController::class, 'setPrimaryImage'])
        ->name('properties.set-primary-image');
         // Nouvelle route pour l'ordre des images
    Route::post('properties/{property}/update-image-order', [PropertyController::class, 'updateImageOrder'])
    ->name('properties.update-image-order');
    Route::get('properties/{property}/availability-suggestions',
        [PropertyController::class, 'getAvailabilitySuggestions'])
        ->name('properties.availability-suggestions');

    Route::get('/payments/{payment}/download-proof', [App\Http\Controllers\Client\PaymentController::class, 'downloadProof'])
        ->name('payments.download-proof');
    Route::get('/admin/dashboard/export', [DashboardController::class, 'export'])->name('admin.dashboard.export');

});

Route::name('client.')->prefix('client')->group(function () {

    // Routes pour la connexion et l'inscription

    Route::get('/login', [App\Http\Controllers\Client\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Client\AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [App\Http\Controllers\Client\AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Client\AuthController::class, 'register'])->name('register.post');

    // Route accessible sans authentification
    Route::get('/properties', [App\Http\Controllers\Client\PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/{property}', [App\Http\Controllers\Client\PropertyController::class, 'show'])->name('properties.show');
});

Route::name('client.')->prefix('client')->middleware(['auth'])->group(function () {
    // Routes pour les propriétés
    Route::post('/properties/{property}/reserve', [App\Http\Controllers\Client\PropertyController::class, 'reserve'])->name('properties.reserve');

    // Routes pour les réservations
    Route::get('/reservations', [App\Http\Controllers\Client\ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [App\Http\Controllers\Client\ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/cancel', [App\Http\Controllers\Client\ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Routes pour les paiements
    Route::get('/payments', [App\Http\Controllers\Client\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{reservation}/initiate', [App\Http\Controllers\Client\PaymentController::class, 'initiate'])->name('payments.initiate'); // Nouvelle route
    Route::post('/payments/{reservation}/pay', [App\Http\Controllers\Client\PaymentController::class, 'pay'])->name('payments.pay');
    Route::get('/payments/{payment}', [App\Http\Controllers\Client\PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/download-proof', [App\Http\Controllers\Client\PaymentController::class, 'downloadProof'])
     ->name('payments.download-proof');

    // logout the user
    Route::post('/logout', [App\Http\Controllers\Client\AuthController::class, 'logout'])->name('logout');
});



Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



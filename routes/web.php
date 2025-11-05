<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Guest (Public)
|--------------------------------------------------------------------------
*/

// Halaman publik untuk tamu
Route::get('/', 'GuestController@index')->name('guest.index');
Route::post('/guest/store', 'GuestController@store')->name('guest.store');
Route::get('/guest/success', 'GuestController@success')->name('guest.success');
Route::get('/guest/checkout', 'GuestController@checkoutPage')->name('guest.checkout.page');
Route::post('/guest/checkout-by-phone', 'GuestController@checkoutByPhone')->name('guest.checkout.by-phone');
Route::post('/guest/checkout/{id}', 'GuestController@checkout')->name('guest.checkout');

/*
|--------------------------------------------------------------------------
| Web Routes - Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

/*
|--------------------------------------------------------------------------
| Web Routes - Authenticated Users
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function() {
    
    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | Receptionist Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:receptionist'])->prefix('receptionist')->name('receptionist.')->group(function() {
        Route::get('/guests', 'ReceptionistController@index')->name('guests.index');
        Route::get('/guest/{id}', 'ReceptionistController@show')->name('guest.show');
        Route::post('/guest/{id}/verify', 'ReceptionistController@verify')->name('guest.verify');
        Route::post('/guest/{id}/reject', 'ReceptionistController@reject')->name('guest.reject');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Employee Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:employee'])->prefix('employee')->name('employee.')->group(function() {
        Route::get('/guests', 'EmployeeController@index')->name('guests.index');
        Route::get('/guest/{id}', 'EmployeeController@show')->name('guest.show');
        Route::post('/guest/{id}/start-meeting', 'EmployeeController@startMeeting')->name('guest.start-meeting');
        Route::post('/guest/{id}/checkout', 'EmployeeController@checkout')->name('guest.checkout');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function() {
    // User Management
    Route::get('/users', 'Admin\UserController@index')->name('users.index');
    Route::get('/users/create', 'Admin\UserController@create')->name('users.create');
    Route::post('/users', 'Admin\UserController@store')->name('users.store');
    Route::get('/users/{id}/edit', 'Admin\UserController@edit')->name('users.edit');
    Route::put('/users/{id}', 'Admin\UserController@update')->name('users.update');
    Route::delete('/users/{id}', 'Admin\UserController@destroy')->name('users.destroy');
    
    // Guest Management
    Route::get('/guests', 'Admin\GuestController@index')->name('guests.index');
    Route::get('/guest/{id}', 'Admin\GuestController@show')->name('guest.show');
    Route::delete('/guest/{id}', 'Admin\GuestController@destroy')->name('guest.destroy');
    
    // Reports
    Route::get('/reports', 'Admin\ReportController@index')->name('reports.index');
    Route::get('/reports/export', 'Admin\ReportController@export')->name('reports.export');
});
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminTopupController;
use App\Http\Controllers\ManageVehicleController;
use App\Http\Controllers\UserLoginManagementController;
use App\Http\Controllers\CustomerRegistrationController;

Route::get('/', function () {
    return view('publichomepage');
});


//-------------------admin routes--------------------------------------------------------------------------------------------------
Route::middleware(['verify.firebase.session', 'auth'])->group(function () {

    //admin dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.admindashboard');
    })->name('admin.dashboard');
});
//-------------------end admin routes-----------------------------------------------------------------------------------------------

//--------------------stationowner routes-------------------------------------------------------------------------------------------
Route::middleware(['verify.firebase.session', 'auth'])->group(function () {

    //station owner dashboard
    Route::get('/stationowner/dashboard', function () {
        return view('stationowner.stationownerdashborad');
    })->name('stationowner.dashboard');

    //manage topups
    Route::get('/admin/topups', [AdminTopupController::class, 'index'])->name('admin.topups');
    Route::post('/admin/topups/{id}/approve', [AdminTopupController::class, 'approve'])->name('admin.topups.approve');
    Route::post('/admin/topups/{id}/reject', [AdminTopupController::class, 'reject'])->name('admin.topups.reject');
    //edit amount of topup
    Route::post('/admin/topups/{id}/update-amount', [AdminTopupController::class, 'updateAmount'])->name('admin.topups.updateAmount');
});

//--------------------end stationowner routes-------------------------------------------------------------------------------------

//-------------------customer routes--------------------------------------------------------------------------------------------------
//reigster customer as a user
//register form view
Route::get('/customer/register', function () {
    return view('customer.registercustomerform');
});
//save customer data
Route::post('/customer/registerdata', [CustomerRegistrationController::class, 'register'])->name('customer.registerdata');
//create session for customer
Route::post('/create-session', [CustomerRegistrationController::class, 'createSession'])->name('createSession');

Route::middleware(['verify.firebase.session', 'auth'])->group(function () {

    //customer dashboard
    Route::get('/customer/dashboard', function () {
        return view('customer.customerdashboard');
    })->name('customer.dashboard');

    //manage vehicles-----------------------------------
    //customer manage vehicles view

    //register vehicle form view
    Route::get('/customer/registervehicle', function () {
        return view('customer.registervehicleformview');
    })->name('customer.registervehicle');
    //save vehicle data
    Route::post('/customer/registervehicledata', [ManageVehicleController::class, 'registerVehicle'])->name('customer.registervehicledata');
    //end manage vehicles-----------------------------------

    //payment proof upload
    Route::get('/customer/payment-proof', [App\Http\Controllers\PaymentProofController::class, 'showForm']);
    Route::post('/customer/upload-payment-proof', [App\Http\Controllers\PaymentProofController::class, 'store']);
    //topup history
    Route::get('/customer/topup-history', [\App\Http\Controllers\TopupController::class, 'customerTopupHistory'])
    ->name('customer.topup.history');

});

//end customer dashboard
//--------------------------end customer routes---------------------------------------------------------------------------------------------

//common routes
Route::get('/login', function () {
    return view('common.login');
})->name('login');
//logout route
// Logout route
Route::post('/logout', [UserLoginManagementController::class, 'logout'])->name('logout');

//dashboard route to redirect after login to dashboards according to user role
Route::get('/dashboard', [UserLoginManagementController::class, 'dashboard'])->name('dashboard')->middleware('verify.firebase.session');
    //------------------------------end common routes------------------------------------------------------------------------------------------------

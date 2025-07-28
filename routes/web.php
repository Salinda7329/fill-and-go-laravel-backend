<?php

use App\Http\Controllers\CustomerRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('publichomepage');
});

//customer routes
//reigster customer as a user
//register form view
Route::get('/customer/register', function () {
    return view('customer.registercustomerform');
});
//save customer data
Route::post('/customer/registerdata',[CustomerRegistrationController::class, 'register'])->name('customer.registerdata');
//create session for customer
Route::post('/create-session', [CustomerRegistrationController::class, 'createSession'])->name('createSession');

//end customer routes

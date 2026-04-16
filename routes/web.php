<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.auth.login');
});


Route::get('/user', function () {
    return view('modules.users.manageUsers');
});


Route::get('/users/manage', function () {
    return view('pages.pages.users.manageUsers');
});
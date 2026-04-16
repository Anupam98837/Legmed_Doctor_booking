<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.auth.login');

Route::view('/dashboard', 'pages.pages.common.dashboard');
Route::view('/profile', 'pages.pages.user.profile');
Route::redirect('/user', '/users/manage');

Route::view('/users/manage', 'pages.pages.users.manageUsers');
Route::view('/user/manage', 'pages.pages.users.manageUsers');

Route::view('/dashboard-menu/manage', 'modules.dashboardMenu.manageDashboardMenu');
Route::view('/dashboard-menu/create', 'modules.dashboardMenu.createDashboardMenu');

Route::view('/page-privilege/manage', 'modules.privileges.managePagePrivileges');
Route::view('/page-privilege/create', 'modules.privileges.createPagePrivileges');

Route::view('/user-privileges/manage', 'modules.privileges.assignPrivileges');
Route::view('/role-privileges/manage', 'modules.privileges.assignRolePrivileges');

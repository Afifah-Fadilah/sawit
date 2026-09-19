<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/sidebar', function () {
    return view('admin.components.sidebar');
});

Route::get('/helo', function () {
    return ('laal nyoba"');
});
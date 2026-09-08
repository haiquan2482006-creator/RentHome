<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Home');
});

Route::get('/Admin', function (){
    return view('Admin.Overview');
});

Route::get('/login', function (){
    return view('dangnhapdk.login');
});

Route::get('/Register',function (){
    return view('dangnhapdk.Register');
});
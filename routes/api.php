<?php

// Api\AuthController@login -> el RouteservideProvider ya declaramos y lo buscara dentro de de controller/Api

Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');

// JSON => Public Resourse
Route::get('/specialties', 'SpecialtyController@index');
Route::get('/specialties/{specialty}/doctors', 'SpecialtyController@doctors');
Route::get('/schedule/hours', 'ScheduleController@hours');

// middleware auth:api verifiac si hay una sesión

Route::middleware('auth:api')->group(function () {
    Route::get('/user', 'UserController@show');
    Route::post('/user', 'UserController@update');
    Route::post('/logout', 'AuthController@logout');

    // appointments
    Route::get('/appointments', 'AppointmentController@index');
    Route::post('/appointments', 'AppointmentController@store');

    // FCM
    Route::post('/fcm/token', 'FirebaseController@postToken');
});


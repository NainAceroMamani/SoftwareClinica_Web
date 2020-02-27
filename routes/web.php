<?php

Route::get('/', function () {
    // return view('welcome');
    return redirect('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth', 'admin'])->namespace('Admin')->group(function() {
    //Specialty
    Route::get('/specialties', 'SpecialtyController@index');
    Route::get('/specialties/create', 'SpecialtyController@create');
    Route::get('/specialties/{specialty}/edit', 'SpecialtyController@edit');

    Route::post('/specialties', 'SpecialtyController@store');
    Route::put('/specialties/{specialty}', 'SpecialtyController@update');
    Route::delete('/specialties/{specialty}', 'SpecialtyController@destroy');

    // Medicos
    Route::resource('doctors','DoctorController');

    // Pacientes
    Route::resource('patients','PatientController');

    // Charts
    Route::get('/charts/appointments/line', 'ChartController@appointements');
    Route::get('/charts/doctors/column', 'ChartController@doctors');
    Route::get('/charts/doctors/column/data', 'ChartController@doctorsJson');
});

Route::middleware(['auth', 'doctor'])->namespace('Doctor')->group(function() {
    Route::get('/schedule', 'ScheduleController@edit');
    Route::post('/schedule', 'ScheduleController@store');
    
    // Mis pacientes
    Route::get('/patient', 'ScheduleController@index');
});

Route::middleware('auth')->group(function() {
    Route::get('/appointments/create', 'AppointmentController@create');
    Route::post('/appointments', 'AppointmentController@store');

    Route::get('/appointments', 'AppointmentController@index');
    Route::get('/appointments/{appointment}', 'AppointmentController@show');
    
    Route::get('/appointments/{appointment}/cancel', 'AppointmentController@showCancelForm');
    Route::post('/appointments/{appointment}/cancel', 'AppointmentController@postCancel');
    Route::post('/appointments/{appointment}/confirm', 'AppointmentController@postConfirm');
    
    // JSON
    Route::get('/specialties/{specialty}/doctors', 'Api\SpecialtyController@doctors');
    Route::get('/schedule/hours', 'Api\ScheduleController@hours');
});


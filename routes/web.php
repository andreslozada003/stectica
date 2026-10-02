<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard', [
        'today' => now()->locale('es')->isoFormat('dddd, D [de] MMMM'),
        'appointments' => [
            ['time' => '09:00', 'patient' => 'Valentina Ríos', 'service' => 'Armonización facial', 'specialist' => 'Dra. Ángela Cruz', 'status' => 'Confirmada'],
            ['time' => '10:30', 'patient' => 'Laura Martínez', 'service' => 'Limpieza facial premium', 'specialist' => 'Sofía Ramírez', 'status' => 'Programada'],
            ['time' => '12:00', 'patient' => 'Mariana Torres', 'service' => 'Toxina botulínica', 'specialist' => 'Dra. Ángela Cruz', 'status' => 'Confirmada'],
            ['time' => '15:30', 'patient' => 'Camila Gómez', 'service' => 'Depilación láser', 'specialist' => 'Natalia Pérez', 'status' => 'Programada'],
        ],
    ]);
})->name('dashboard');

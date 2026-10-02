<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/iniciar-sesion', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/iniciar-sesion', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::post('/cerrar-sesion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::post('/webhooks/whatsapp', WhatsAppWebhookController::class)->name('webhooks.whatsapp');
Route::get('/recuperar-contrasena', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/recuperar-contrasena', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
Route::get('/restablecer-contrasena/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/restablecer-contrasena', [NewPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.store');

Route::get('/dashboard', function () {
    return view('dashboard', [
        'today' => now()->locale('es')->isoFormat('dddd, D [de] MMMM'),
        'appointments' => [
            ['time' => '09:00', 'patient' => 'Valentina Ríos', 'service' => 'Armonización facial', 'specialist' => 'Dra. Ángela Cruz', 'status' => 'Confirmada'],
            ['time' => '10:30', 'patient' => 'Laura Martínez', 'service' => 'Limpieza facial premium', 'specialist' => 'Sofía Ramírez', 'status' => 'Programada'],
            ['time' => '12:00', 'patient' => 'Mariana Torres', 'service' => 'Toxina botulínica', 'specialist' => 'Dra. Ángela Cruz', 'status' => 'Confirmada'],
            ['time' => '15:30', 'patient' => 'Camila Gómez', 'service' => 'Depilación láser', 'specialist' => 'Natalia Pérez', 'status' => 'Programada'],
        ],
    ]);
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/citas/nueva', [AgendaController::class, 'create'])->name('agenda.create');
    Route::get('/agenda/disponibilidad', [AgendaController::class, 'availability'])->name('agenda.availability');
    Route::post('/agenda/citas', [AgendaController::class, 'store'])->name('agenda.store');
    Route::patch('/agenda/citas/{appointment}/estado', [AgendaController::class, 'updateStatus'])->name('agenda.status');
});

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppWebhookController extends Controller
{
    public function __invoke(Request $request, AppointmentService $appointments): Response
    {
        $body = mb_strtolower(trim((string) $request->input('Body')));
        $phone = preg_replace('/\D+/', '', (string) $request->input('From'));
        $appointment = Appointment::query()->whereIn('status', ['programada', 'confirmada'])->whereHas('patient', fn ($q) => $q->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), ' ', ''), '-', ''), '(', '') LIKE ?", ["%{$phone}%"]))->where('starts_at', '>=', now())->orderBy('starts_at')->first();
        if (! $appointment) return response('', 204);
        if (in_array($body, ['1', 'confirmar', 'confirmo', 'confirmada'], true)) $appointments->changeStatus($appointment, 'confirmada', null, 'Confirmación automática de WhatsApp');
        if (in_array($body, ['2', 'cancelar', 'cancelo', 'cancelada'], true)) $appointments->changeStatus($appointment, 'cancelada', null, 'Cancelación automática de WhatsApp');
        return response('', 204);
    }
}

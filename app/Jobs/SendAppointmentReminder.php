<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\AppointmentNotificationLog;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public int $appointmentId, public string $kind) {}
    public function handle(WhatsAppService $whatsApp): void
    {
        $appointment = Appointment::query()->with(['patient', 'treatment'])->findOrFail($this->appointmentId);
        if (! $appointment->send_reminder || ! in_array($appointment->status, Appointment::ACTIVE_STATUSES, true)) return;
        $message = "Hola {$appointment->patient->first_name}, te recordamos tu cita de {$appointment->treatment->name} el {$appointment->starts_at->format('d/m/Y H:i')}. Responde 1 para confirmar o 2 para cancelar.";
        if (in_array('email', $appointment->reminder_channels ?? [], true) && $appointment->patient->email) $this->send('email', fn () => Mail::raw($message, fn ($mail) => $mail->to($appointment->patient->email)->subject('Recordatorio de cita')));
        if (in_array('whatsapp', $appointment->reminder_channels ?? [], true) && $appointment->patient->phone) $this->send('whatsapp', fn () => $whatsApp->send($appointment->patient->phone, $message));
    }
    private function send(string $channel, callable $action): void
    {
        try { $action(); AppointmentNotificationLog::query()->create(['appointment_id' => $this->appointmentId, 'channel' => $channel, 'kind' => $this->kind, 'status' => 'sent', 'sent_at' => now()]); }
        catch (Throwable $exception) { AppointmentNotificationLog::query()->updateOrCreate(['appointment_id' => $this->appointmentId, 'channel' => $channel, 'kind' => $this->kind], ['status' => 'failed', 'error' => $exception->getMessage()]); throw $exception; }
    }
}

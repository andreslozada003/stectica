<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use Illuminate\Console\Command;

class DispatchAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Encola recordatorios de citas a 24 y 2 horas.';
    public function handle(): int
    {
        foreach (['reminder_24h' => 24, 'reminder_2h' => 2] as $kind => $hours) {
            Appointment::query()->whereIn('status', Appointment::ACTIVE_STATUSES)->whereBetween('starts_at', [now()->addHours($hours)->subMinutes(5), now()->addHours($hours)->addMinutes(5)])->whereDoesntHave('notificationLogs', fn ($q) => $q->where('kind', $kind)->where('status', 'sent'))->pluck('id')->each(fn (int $id) => SendAppointmentReminder::dispatch($id, $kind));
        }
        return self::SUCCESS;
    }
}

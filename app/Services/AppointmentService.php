<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AgendaBlock;
use App\Models\Appointment;
use App\Models\Box;
use App\Models\TreatmentCatalog;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    /** @param array<string, mixed> $data */
    public function create(array $data, ?User $actor = null): Appointment
    {
        return DB::transaction(function () use ($data, $actor): Appointment {
            $treatment = TreatmentCatalog::query()->findOrFail($data['treatment_catalog_id']);
            $startsAt = CarbonImmutable::parse($data['starts_at']);
            // ends_at represents the resource release time, including sanitization.
            $endsAt = $startsAt->addMinutes($treatment->duration_minutes + $treatment->sanitization_minutes);

            User::query()->whereKey($data['user_id'])->lockForUpdate()->firstOrFail();
            $box = Box::query()->whereKey($data['box_id'])->lockForUpdate()->firstOrFail();
            $this->assertAvailability((int) $data['user_id'], $box, $treatment, $startsAt, $endsAt);

            $appointment = Appointment::query()->create([
                ...$data,
                'ends_at' => $endsAt,
                'price' => $data['price'] ?? $treatment->base_price,
                'status' => $data['status'] ?? 'programada',
                'send_reminder' => $data['send_reminder'] ?? false,
                'reminder_channels' => $data['reminder_channels'] ?? [],
            ]);
            $appointment->statusHistory()->create(['to_status' => $appointment->status, 'changed_by' => $actor?->id, 'note' => 'Cita creada']);

            return $appointment;
        });
    }

    public function changeStatus(Appointment $appointment, string $status, ?User $actor = null, ?string $note = null): Appointment
    {
        if (! in_array($status, $this->statuses(), true)) {
            throw ValidationException::withMessages(['status' => 'Estado de cita no válido.']);
        }
        if ($status === 'cancelada' && blank($note)) {
            throw ValidationException::withMessages(['cancellation_reason' => 'Indica el motivo de cancelación.']);
        }

        return DB::transaction(function () use ($appointment, $status, $actor, $note): Appointment {
            $appointment->refresh();
            $previous = $appointment->status;
            $appointment->update(['status' => $status, 'cancellation_reason' => $status === 'cancelada' ? $note : $appointment->cancellation_reason]);
            $appointment->statusHistory()->create(['from_status' => $previous, 'to_status' => $status, 'changed_by' => $actor?->id, 'note' => $note]);

            return $appointment;
        });
    }

    /** @return list<string> */
    public function statuses(): array
    {
        return ['programada', 'confirmada', 'en_espera', 'en_atencion', 'realizada', 'cancelada', 'no_asistio', 'reagendada'];
    }

    private function assertAvailability(int $userId, Box $box, TreatmentCatalog $treatment, CarbonImmutable $startsAt, CarbonImmutable $endsAt): void
    {
        if (! $box->active || ! $box->treatments()->whereKey($treatment->id)->exists()) {
            throw ValidationException::withMessages(['box_id' => 'El box no está habilitado para este tratamiento.']);
        }
        $day = $startsAt->dayOfWeek;
        $startTime = $startsAt->format('H:i:s'); $endTime = $endsAt->format('H:i:s');
        $withinSchedule = User::findOrFail($userId)->schedules()->where('active', true)->where('day_of_week', $day)->whereTime('starts_at', '<=', $startTime)->whereTime('ends_at', '>=', $endTime)->exists();
        if (! $withinSchedule) throw ValidationException::withMessages(['starts_at' => 'El especialista no atiende en ese horario.']);
        if (User::findOrFail($userId)->breaks()->where('day_of_week', $day)->whereTime('starts_at', '<', $endTime)->whereTime('ends_at', '>', $startTime)->exists()) throw ValidationException::withMessages(['starts_at' => 'El horario coincide con un descanso del especialista.']);

        $overlap = fn ($query) => $query->where('starts_at', '<', $endsAt)->where('ends_at', '>', $startsAt);
        if (Appointment::query()->whereIn('status', Appointment::ACTIVE_STATUSES)->where($overlap)->where(fn ($q) => $q->where('user_id', $userId)->orWhere('box_id', $box->id))->exists()) throw ValidationException::withMessages(['starts_at' => 'El especialista o el box ya tienen una cita en ese horario.']);
        if (AgendaBlock::query()->where($overlap)->where(fn ($q) => $q->where('user_id', $userId)->orWhere('box_id', $box->id))->exists()) throw ValidationException::withMessages(['starts_at' => 'Existe un bloqueo de agenda para este horario.']);
    }
}

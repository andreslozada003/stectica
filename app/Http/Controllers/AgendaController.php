<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\AgendaBlock;
use App\Models\Box;
use App\Models\Patient;
use App\Models\TreatmentCatalog;
use App\Models\User;
use App\Services\AppointmentService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $date = CarbonImmutable::parse($request->string('date', now()->toDateString())->toString());
        $view = $request->string('view', 'day')->toString();
        $rangeStart = $view === 'month' ? $date->startOfMonth() : ($view === 'week' ? $date->startOfWeek() : $date->startOfDay());
        $rangeEnd = $view === 'month' ? $date->endOfMonth() : ($view === 'week' ? $date->endOfWeek() : $date->endOfDay());
        $appointments = Appointment::query()->with(['patient', 'specialist', 'box', 'treatment'])->whereBetween('starts_at', [$rangeStart, $rangeEnd])->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))->when($request->filled('box_id'), fn ($q) => $q->where('box_id', $request->integer('box_id')))->orderBy('starts_at')->get();
        return view('agenda.index', compact('appointments', 'date', 'view') + ['specialists' => User::query()->orderBy('name')->get(), 'boxes' => Box::query()->active()->orderBy('name')->get()]);
    }

    public function store(StoreAppointmentRequest $request, AppointmentService $service): RedirectResponse
    {
        $service->create($request->validated(), $request->user());
        return redirect()->route('agenda.index')->with('status', 'Cita programada correctamente.');
    }

    public function create(): View
    {
        $patients = Patient::query()
            ->withCount([
                'appointments as completed_appointments_count' => fn ($query) => $query->where('status', 'realizada'),
                'appointments as pending_appointments_count' => fn ($query) => $query->whereIn('status', Appointment::ACTIVE_STATUSES),
                'appointments as cancelled_appointments_count' => fn ($query) => $query->where('status', 'cancelada'),
            ])
            ->orderBy('first_name')->get();

        return view('agenda.create', [
            'patients' => $patients,
            'treatments' => TreatmentCatalog::query()->where('active', true)->orderBy('name')->get(),
            'specialists' => User::query()->orderBy('name')->get(),
            'boxes' => Box::query()->active()->with('treatments:id')->orderBy('name')->get(),
            'defaultDate' => now()->addDay()->toDateString(),
        ]);
    }

    public function availability(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'box_id' => ['required', 'integer', 'exists:boxes,id'],
            'treatment_catalog_id' => ['required', 'integer', 'exists:treatment_catalog,id'],
        ]);

        $treatment = TreatmentCatalog::query()->findOrFail($data['treatment_catalog_id']);
        $box = Box::query()->with('treatments:id')->findOrFail($data['box_id']);
        if (! $box->active || ! $box->treatments->contains('id', $treatment->id)) return response()->json(['slots' => []]);

        $date = CarbonImmutable::parse($data['date'])->startOfDay();
        $duration = $treatment->duration_minutes + $treatment->sanitization_minutes;
        $specialist = User::query()->findOrFail($data['user_id']);
        $schedules = $specialist->schedules()->where('active', true)->where('day_of_week', $date->dayOfWeek)->get();
        $breaks = $specialist->breaks()->where('day_of_week', $date->dayOfWeek)->get();
        $appointments = Appointment::query()->whereIn('status', Appointment::ACTIVE_STATUSES)->whereDate('starts_at', $date)->where(fn ($q) => $q->where('user_id', $specialist->id)->orWhere('box_id', $box->id))->get(['starts_at', 'ends_at']);
        $blocks = AgendaBlock::query()->whereDate('starts_at', $date)->where(fn ($q) => $q->where('user_id', $specialist->id)->orWhere('box_id', $box->id))->get(['starts_at', 'ends_at']);
        $conflicts = $appointments->concat($blocks); $slots = [];
        foreach ($schedules as $schedule) {
            $cursor = $date->setTimeFromTimeString($schedule->starts_at); $limit = $date->setTimeFromTimeString($schedule->ends_at);
            while ($cursor->addMinutes($duration)->lessThanOrEqualTo($limit)) {
                $end = $cursor->addMinutes($duration);
                $hasBreak = $breaks->contains(fn ($break) => $break->starts_at < $end->format('H:i:s') && $break->ends_at > $cursor->format('H:i:s'));
                $hasConflict = $conflicts->contains(fn ($item) => $item->starts_at < $end && $item->ends_at > $cursor);
                if (! $hasBreak && ! $hasConflict && $cursor->isFuture()) $slots[] = $cursor->format('H:i');
                $cursor = $cursor->addMinutes(30);
            }
        }
        return response()->json(['slots' => array_values(array_unique($slots))]);
    }

    public function updateStatus(Request $request, Appointment $appointment, AppointmentService $service): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'string'], 'note' => ['nullable', 'string']]);
        $service->changeStatus($appointment, $data['status'], $request->user(), $data['note'] ?? null);
        return back()->with('status', 'Estado de cita actualizado.');
    }
}

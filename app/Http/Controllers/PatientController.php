<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('buscar')->toString());
        $patients = Patient::query()
            ->withMax('appointments as last_appointment_at', 'starts_at')
            ->when($search !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('document_number', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")))
            ->orderBy('first_name')->orderBy('last_name')->paginate(12)->withQueryString();

        return view('patients.index', [
            'patients' => $patients, 'search' => $search,
            'totalPatients' => Patient::count(), 'activePatients' => Patient::where('status', 'activo')->count(),
            'todayAppointments' => Appointment::whereDate('starts_at', today())->count(),
            'newPatients' => Patient::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ]);
    }

    public function create(): View { return view('patients.create'); }

    public function store(Request $request): RedirectResponse
    {
        $patient = Patient::create($this->patientData($request));
        return redirect()->route('patients.show', $patient)->with('status', 'Paciente registrado correctamente.');
    }

    public function show(Patient $patient): View
    {
        $patient->load([
            'appointments' => fn ($query) => $query->with(['treatment', 'specialist'])->latest('starts_at'),
            'notes.author', 'payments' => fn ($query) => $query->latest('paid_at'), 'photos',
        ]);
        $nextAppointment = $patient->appointments->first(fn (Appointment $appointment) => $appointment->starts_at->isFuture() && in_array($appointment->status, Appointment::ACTIVE_STATUSES, true));
        $completed = $patient->appointments->where('status', 'realizada');
        $total = (float) $patient->appointments->sum('price');
        $paid = (float) $patient->appointments->sum('deposit_paid') + (float) $patient->payments->sum('amount');

        return view('patients.show', compact('patient', 'nextAppointment', 'completed', 'total', 'paid'));
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $patient->update($this->patientData($request, $patient));
        return back()->with('status', 'Datos del paciente actualizados.');
    }

    public function updateClinical(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'medical_history' => ['nullable', 'string'], 'allergies' => ['nullable', 'string'], 'medications' => ['nullable', 'string'],
            'contraindications' => ['nullable', 'string'], 'clinical_observations' => ['nullable', 'string'],
        ]);
        $patient->update($data);
        return back()->with('status', 'Historia clínica actualizada.');
    }

    public function storeNote(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:3000']]);
        $patient->notes()->create($data + ['user_id' => $request->user()->id]);
        return back()->with('status', 'Nota registrada.');
    }

    public function storePayment(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate(['amount' => ['required', 'numeric', 'min:0.01'], 'method' => ['required', 'string', 'max:30'], 'note' => ['nullable', 'string', 'max:500']]);
        $patient->payments()->create($data + ['received_by' => $request->user()->id, 'paid_at' => now()]);
        return back()->with('status', 'Pago registrado.');
    }

    private function patientData(Request $request, ?Patient $patient = null): array
    {
        return $request->validate([
            'document_type' => ['required', 'string', 'max:10'],
            'document_number' => ['required', 'string', 'max:50', Rule::unique('patients', 'document_number')->ignore($patient)],
            'first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:today'], 'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'], 'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['activo', 'pendiente', 'inactivo'])],
        ]);
    }
}

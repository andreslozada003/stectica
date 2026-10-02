<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array
    {
        return ['patient_id' => ['required', 'integer', 'exists:patients,id'], 'user_id' => ['required', 'integer', 'exists:users,id'], 'box_id' => ['required', 'integer', 'exists:boxes,id'], 'treatment_catalog_id' => ['required', 'integer', 'exists:treatment_catalog,id'], 'starts_at' => ['required', 'date', 'after:now'], 'internal_notes' => ['nullable', 'string'], 'price' => ['nullable', 'numeric', 'min:0'], 'deposit_paid' => ['nullable', 'numeric', 'min:0'], 'send_reminder' => ['nullable', 'boolean'], 'reminder_channels' => ['nullable', 'array'], 'reminder_channels.*' => ['in:email,whatsapp']];
    }
}

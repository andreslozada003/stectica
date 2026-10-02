<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('patients')) Schema::create('patients', function (Blueprint $table): void {
            $table->id(); $table->string('document_type', 10)->default('CC'); $table->string('document_number')->unique(); $table->string('first_name'); $table->string('last_name'); $table->date('birth_date')->nullable(); $table->string('phone', 30); $table->string('email')->nullable()->index(); $table->text('address')->nullable(); $table->text('medical_history')->nullable(); $table->text('allergies')->nullable(); $table->timestamps(); $table->softDeletes();
        });
        if (! Schema::hasTable('treatment_catalog')) Schema::create('treatment_catalog', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->text('description')->nullable(); $table->unsignedInteger('duration_minutes')->default(60); $table->unsignedInteger('sanitization_minutes')->default(0); $table->decimal('base_price', 12, 2)->default(0); $table->boolean('active')->default(true)->index(); $table->timestamps();
        });
        if (! Schema::hasColumn('treatment_catalog', 'sanitization_minutes')) Schema::table('treatment_catalog', function (Blueprint $table): void { $table->unsignedInteger('sanitization_minutes')->default(0)->after('duration_minutes'); });
        Schema::create('boxes', function (Blueprint $table): void {
            $table->id(); $table->string('name')->unique(); $table->string('type')->nullable(); $table->boolean('active')->default(true)->index(); $table->timestamps();
        });
        Schema::create('box_treatment', function (Blueprint $table): void {
            $table->foreignId('box_id')->constrained()->cascadeOnDelete(); $table->foreignId('treatment_catalog_id')->constrained('treatment_catalog')->cascadeOnDelete(); $table->primary(['box_id', 'treatment_catalog_id']);
        });
        Schema::create('staff_schedules', function (Blueprint $table): void {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->unsignedTinyInteger('day_of_week')->comment('0 domingo, 6 sábado'); $table->time('starts_at'); $table->time('ends_at'); $table->boolean('active')->default(true); $table->unique(['user_id', 'day_of_week', 'starts_at']);
        });
        Schema::create('staff_breaks', function (Blueprint $table): void {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->unsignedTinyInteger('day_of_week'); $table->time('starts_at'); $table->time('ends_at'); $table->unique(['user_id', 'day_of_week', 'starts_at']);
        });
        Schema::create('appointments', function (Blueprint $table): void {
            $table->id(); $table->foreignId('patient_id')->constrained()->restrictOnDelete(); $table->foreignId('user_id')->constrained()->restrictOnDelete(); $table->foreignId('box_id')->constrained()->restrictOnDelete(); $table->foreignId('treatment_catalog_id')->constrained('treatment_catalog')->restrictOnDelete(); $table->dateTime('starts_at')->index(); $table->dateTime('ends_at'); $table->enum('status', ['programada', 'confirmada', 'en_espera', 'en_atencion', 'realizada', 'cancelada', 'no_asistio', 'reagendada'])->default('programada')->index(); $table->text('cancellation_reason')->nullable(); $table->text('internal_notes')->nullable(); $table->decimal('price', 12, 2)->default(0); $table->decimal('deposit_paid', 12, 2)->default(0); $table->timestamps(); $table->index(['user_id', 'starts_at', 'ends_at'], 'appointments_user_time_index'); $table->index(['box_id', 'starts_at', 'ends_at'], 'appointments_box_time_index');
        });
        Schema::create('agenda_blocks', function (Blueprint $table): void {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $table->foreignId('box_id')->nullable()->constrained()->nullOnDelete(); $table->enum('type', ['absence', 'vacation', 'maintenance', 'cleaning', 'other'])->index(); $table->string('reason'); $table->dateTime('starts_at'); $table->dateTime('ends_at'); $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps(); $table->index(['starts_at', 'ends_at']);
        });
        Schema::create('appointment_status_histories', function (Blueprint $table): void {
            $table->id(); $table->foreignId('appointment_id')->constrained()->cascadeOnDelete(); $table->string('from_status')->nullable(); $table->string('to_status'); $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete(); $table->text('note')->nullable(); $table->timestamps(); $table->index(['appointment_id', 'created_at']);
        });
        Schema::create('appointment_notification_logs', function (Blueprint $table): void {
            $table->id(); $table->foreignId('appointment_id')->constrained()->cascadeOnDelete(); $table->enum('channel', ['email', 'whatsapp']); $table->enum('kind', ['reminder_24h', 'reminder_2h']); $table->enum('status', ['sent', 'failed']); $table->text('error')->nullable(); $table->timestamp('sent_at')->nullable(); $table->timestamps(); $table->unique(['appointment_id', 'channel', 'kind']);
        });
        Schema::create('consents', function (Blueprint $table): void {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete(); $table->string('treatment_name'); $table->longText('content'); $table->string('signature_path')->nullable(); $table->timestamp('signed_at')->nullable(); $table->foreignId('created_by')->constrained('users')->restrictOnDelete(); $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_notification_logs'); Schema::dropIfExists('appointment_status_histories'); Schema::dropIfExists('agenda_blocks'); Schema::dropIfExists('appointments'); Schema::dropIfExists('staff_breaks'); Schema::dropIfExists('staff_schedules'); Schema::dropIfExists('box_treatment'); Schema::dropIfExists('boxes'); Schema::dropIfExists('consents'); Schema::dropIfExists('treatment_catalog'); Schema::dropIfExists('patients');
    }
};

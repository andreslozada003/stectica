<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table): void {
            $table->string('status', 20)->default('activo')->index()->after('phone');
            $table->text('medications')->nullable()->after('allergies');
            $table->text('contraindications')->nullable()->after('medications');
            $table->text('clinical_observations')->nullable()->after('contraindications');
        });

        Schema::create('patient_notes', function (Blueprint $table): void {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body'); $table->timestamps();
        });
        Schema::create('patient_payments', function (Blueprint $table): void {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 12, 2); $table->string('method', 30)->default('efectivo');
            $table->text('note')->nullable(); $table->timestamp('paid_at'); $table->timestamps();
        });
        Schema::create('patient_photos', function (Blueprint $table): void {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('path'); $table->string('caption')->nullable(); $table->timestamp('taken_at')->nullable(); $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_photos'); Schema::dropIfExists('patient_payments'); Schema::dropIfExists('patient_notes');
        Schema::table('patients', function (Blueprint $table): void { $table->dropColumn(['status', 'medications', 'contraindications', 'clinical_observations']); });
    }
};

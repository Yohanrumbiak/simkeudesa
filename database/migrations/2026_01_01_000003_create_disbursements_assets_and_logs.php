<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disbursement_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->unsignedSmallInteger('fiscal_year')->default(2026);
            $table->string('application_number')->unique();
            $table->string('title');
            $table->string('phase'); // Tahap I (40%), Tahap II (40%), etc.
            $table->decimal('requested_amount', 15, 2);
            $table->date('submission_date');
            $table->enum('status', ['menunggu_verifikasi', 'perlu_revisi', 'lengkap', 'ditolak', 'disalurkan'])->default('menunggu_verifikasi');
            $table->text('review_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('disbursement_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disbursement_application_id')->constrained('disbursement_applications')->cascadeOnDelete();
            $table->string('document_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('file_type')->default('pdf');
            $table->enum('status', ['menunggu_verifikasi', 'perlu_revisi', 'lengkap', 'ditolak'])->default('menunggu_verifikasi');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('disbursement_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disbursement_application_id')->constrained('disbursement_applications')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->string('status_before');
            $table->string('status_after');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('village_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('category'); // Tanah, Peralatan & Mesin, Gedung & Bangunan, Jalan/Irigasi, Aset Lainnya
            $table->unsignedSmallInteger('acquisition_year');
            $table->decimal('acquisition_value', 15, 2);
            $table->integer('quantity')->default(1);
            $table->string('unit')->default('Unit');
            $table->string('location');
            $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->text('maintenance_notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->string('action');
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('activity_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('Batas Pelaporan'); // Batas Pelaporan, Musyawarah, Penyaluran Dana, Evaluasi
            $table->date('deadline_date');
            $table->enum('status', ['akan_datang', 'berlangsung', 'selesai'])->default('akan_datang');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->string('role_target')->nullable(); // admin, kepala_desa, pimpinan
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'success', 'danger'])->default('info');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_schedules');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('village_assets');
        Schema::dropIfExists('disbursement_reviews');
        Schema::dropIfExists('disbursement_documents');
        Schema::dropIfExists('disbursement_applications');
    }
};

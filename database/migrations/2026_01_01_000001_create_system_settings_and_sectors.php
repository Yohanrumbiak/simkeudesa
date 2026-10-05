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
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Pemerintah Kabupaten Bandung Barat');
            $table->string('agency')->default('Dinas Pemberdayaan Masyarakat dan Desa (DPMD)');
            $table->string('system_name')->default('SIMKeuDesa');
            $table->string('system_subtitle')->default('Sistem Informasi Manajemen Keuangan Desa');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->unsignedSmallInteger('active_fiscal_year')->default(2026);
            $table->timestamps();
        });

        Schema::create('fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('budget_sectors', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 01, 02, 03, 04, 05
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('#3b82f6');
            $table->string('icon')->default('fa-folder');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_sectors');
        Schema::dropIfExists('fiscal_years');
        Schema::dropIfExists('institutions');
    }
};

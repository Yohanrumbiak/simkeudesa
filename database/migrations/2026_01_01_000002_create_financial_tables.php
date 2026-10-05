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
        Schema::create('apbdes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->unsignedSmallInteger('fiscal_year')->default(2026);
            $table->string('title');
            $table->string('document_number')->nullable();
            $table->enum('type', ['murni', 'perubahan'])->default('murni');
            $table->decimal('total_revenue_budget', 15, 2)->default(0);
            $table->decimal('total_expenditure_budget', 15, 2)->default(0);
            $table->decimal('total_financing_budget', 15, 2)->default(0);
            $table->enum('status', ['draft', 'diajukan', 'diverifikasi', 'disetujui', 'ditolak'])->default('disetujui');
            $table->date('approval_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });

        Schema::create('apbdes_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apbdes_id')->constrained('apbdes')->cascadeOnDelete();
            $table->enum('type', ['pendapatan', 'belanja', 'pembiayaan']);
            $table->foreignId('budget_sector_id')->nullable()->constrained('budget_sectors')->nullOnDelete();
            $table->string('account_code');
            $table->string('activity_name');
            $table->decimal('original_amount', 15, 2)->default(0);
            $table->decimal('revised_amount', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->unsignedSmallInteger('fiscal_year')->default(2026);
            $table->string('transaction_number')->unique();
            $table->date('date');
            $table->string('funding_source'); // Dana Desa (DD), Alokasi Dana Desa (ADD), Pendapatan Asli Desa (PADes), etc.
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('payer')->nullable();
            $table->string('destination_account')->nullable();
            $table->string('supporting_document')->nullable();
            $table->enum('status', ['draft', 'tercatat', 'diverifikasi'])->default('diverifikasi');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('expenditures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->unsignedSmallInteger('fiscal_year')->default(2026);
            $table->string('transaction_number')->unique();
            $table->date('date');
            $table->foreignId('budget_sector_id')->constrained('budget_sectors');
            $table->foreignId('apbdes_item_id')->nullable()->constrained('apbdes_items')->nullOnDelete();
            $table->string('activity_name');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('payee');
            $table->string('payment_method')->default('Transfer Bank');
            $table->string('supporting_document')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'diverifikasi', 'dibayar', 'ditolak'])->default('dibayar');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenditures');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('apbdes_items');
        Schema::dropIfExists('apbdes');
    }
};

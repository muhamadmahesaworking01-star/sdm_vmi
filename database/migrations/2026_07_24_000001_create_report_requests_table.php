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
        Schema::create('report_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('report_type'); // employee_list, payroll_summary, contract_recap, sdm_performance
            $table->string('filter_divisi')->nullable();
            $table->string('filter_kampus')->nullable();
            $table->date('filter_date_from')->nullable();
            $table->date('filter_date_to')->nullable();
            $table->string('format')->default('pdf'); // pdf, excel, csv
            $table->string('status')->default('pending'); // pending, processing, ready, failed, expired
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('generated_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_requests');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('announcements', function (Blueprint $table) { $table->string('target_role', 30)->default('semua')->after('content'); }); } public function down(): void { Schema::table('announcements', fn (Blueprint $table) => $table->dropColumn('target_role')); } };

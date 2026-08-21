<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nip_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('category_code', 2);
            $table->string('entry_year', 4);
            $table->string('birth_date', 6);
            $table->unsignedSmallInteger('last_number');
            $table->timestamps();
            $table->unique(['category_code', 'entry_year', 'birth_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nip_sequences');
    }
};

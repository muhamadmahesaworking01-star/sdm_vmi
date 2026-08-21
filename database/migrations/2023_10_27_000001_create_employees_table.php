<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip')->unique();
            $table->enum('peran', ['pengajar', 'karyawan']);
            $table->string('email')->unique();
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
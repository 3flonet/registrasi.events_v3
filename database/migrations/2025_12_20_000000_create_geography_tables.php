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
        // Tabel Countries
        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $blueprint) {
                $blueprint->id();
                $blueprint->string('name');
                // Tambahkan timestamps jika ingin, tapi model menonaktifkannya
                // $blueprint->timestamps(); 
            });
        }

        // Tabel States
        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $blueprint) {
                $blueprint->id();
                $blueprint->string('name');
                $blueprint->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            });
        }

        // Tabel Cities
        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $blueprint) {
                $blueprint->id();
                $blueprint->string('name');
                $blueprint->foreignId('state_id')->constrained('states')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
    }
};

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
        Schema::table('event_agendas', function (Blueprint $table) {
            $table->dropColumn(['location', 'speaker']);
            $table->string('banner_path')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_agendas', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->string('speaker')->nullable();
            $table->dropColumn('banner_path');
        });
    }
};

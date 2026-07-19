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
        Schema::create('message_template_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('icon')->default('fa-folder');
            $table->string('color')->default('slate');
            $table->string('description')->nullable();
            $table->boolean('is_manual_sendable')->default(true);
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_template_categories');
    }
};

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
        Schema::create('inquiry_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_form_id')->constrained()->cascadeOnDelete();
            $table->json('name'); // Translatable
            $table->json('description')->nullable(); // Translatable
            $table->decimal('price', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_categories');
    }
};

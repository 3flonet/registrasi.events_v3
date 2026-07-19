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
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category', 50);
            $table->string('language_code', 10)->default('id');
            $table->text('body_preview')->nullable();
            $table->json('parameters');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('event_email_templates', function (Blueprint $table) {
            $table->foreignId('whatsapp_template_id')->nullable()->constrained('whatsapp_templates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_email_templates', function (Blueprint $table) {
            $table->dropForeign(['whatsapp_template_id']);
            $table->dropColumn('whatsapp_template_id');
        });

        Schema::dropIfExists('whatsapp_templates');
    }
};

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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // e.g., "Medical Certificate", "Consent Form"
            $table->string('slug')->unique();           // e.g., "medical_certificate", "consent_form"
            $table->string('type')->default('document'); // Type categorization (document, form, etc.)
            $table->longText('content');                // HTML content with placeholders
            $table->json('placeholders')->nullable();   // Available placeholders like {patient_name}, {date}
            $table->boolean('is_active')->default(true); // Enable/disable templates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};

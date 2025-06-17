<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // e.g., 'nadi'
            $table->enum('category', ['checkup-info', 'diagnosis', 'treatment']);
            $table->integer('display_order')->default(0);
            $table->json('extra_attributes')->nullable(); // e.g., {"type": "textarea", "required": true}
            $table->timestamps();

            $table->unique(['name', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('presets', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('field_id');
            $table->string('button_text', 255);
            $table->text('preset_text')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('field_id')->references('id')->on('fields');
            $table->unique(['field_id', 'button_text']);
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('presets');
    }
};

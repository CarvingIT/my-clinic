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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('patient_id')->unique()->nullable();
            $table->string('gender')->nullable();
             $table->date('birthdate')->nullable();
             $table->string('email_id')->nullable();
             $table->string('vishesh')->nullable();
             $table->decimal('balance', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['patient_id','gender', 'birthdate', 'email_id','vishesh','balance']);
        });
    }
};

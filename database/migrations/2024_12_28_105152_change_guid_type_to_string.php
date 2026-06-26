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
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('guid');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->string('guid')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('guid');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->uuid('guid')->nullable();
        });
    }
};

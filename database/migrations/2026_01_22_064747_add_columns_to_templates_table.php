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
        Schema::table('templates', function (Blueprint $table) {
            if (!Schema::hasColumn('templates', 'type')) {
                $table->string('type')->default('document')->after('slug');
            }
            if (!Schema::hasColumn('templates', 'placeholders')) {
                $table->json('placeholders')->nullable()->after('content');
            }
            if (!Schema::hasColumn('templates', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('placeholders');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            if (Schema::hasColumn('templates', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('templates', 'placeholders')) {
                $table->dropColumn('placeholders');
            }
            if (Schema::hasColumn('templates', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};

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
        Schema::table('jurisdictions', function (Blueprint $table) {
            Schema::table('jurisdictions', function (Blueprint $table) {
                $table->boolean('is_system_default')->default(false);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurisdictions', function (Blueprint $table) {
            //
        });
    }
};

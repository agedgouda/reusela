<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->uuid('jurisdiction_id'); // UUID foreign key
            $table->foreign('jurisdiction_id')
                ->references('id')
                ->on('jurisdictions')
                ->cascadeOnDelete();
            $table->foreignId('section_title_id')->constrained()->cascadeOnDelete();
            $table->text('text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};

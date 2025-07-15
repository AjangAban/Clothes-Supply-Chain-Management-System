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
        Schema::create('work_in_progress', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('stage'); // e.g., cutting, sewing, finishing
            $table->integer('quantity');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expected_completion')->nullable();
            $table->string('status')->default('in_progress'); // in_progress, completed, paused
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_in_progress');
    }
};

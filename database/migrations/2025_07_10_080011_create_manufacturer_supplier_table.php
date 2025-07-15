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
        Schema::create('manufacturer_supplier', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('manufacturer_id');
    $table->unsignedBigInteger('supplier_id');
    $table->timestamps();

    $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('cascade');
    $table->foreign('supplier_id')->references('id')->on('raw_material_suppliers')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturer_supplier');
    }
};

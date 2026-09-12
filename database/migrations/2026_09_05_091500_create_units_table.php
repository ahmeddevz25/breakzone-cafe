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
        Schema::create('cafe_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit', 100);
            $table->float('quantity')->default(0)->nullable();
            $table->integer('position')->default(0);
            $table->char('status', 1)->default('A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_units');
    }
};

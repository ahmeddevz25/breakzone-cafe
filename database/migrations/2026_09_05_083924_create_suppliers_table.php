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
        Schema::create('cafe_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->nullable();
            $table->string('company', 200)->nullable();
            $table->string('address', 250)->nullable();
            $table->string('mobile', 25)->nullable();
            $table->string('ntn_no', 100)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->char('status', 1)->default('A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_suppliers');
    }
};

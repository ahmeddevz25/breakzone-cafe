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
        Schema::create('cafe_stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(0);
            $table->string('store', 100);
            $table->string('printer_ip_address', 20)->nullable();
            $table->char('printer_port', 5)->default('9100')->nullable();
            $table->integer('opening_balance')->default(0);
            $table->char('status', 1)->default('A');
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_stores');
    }
};

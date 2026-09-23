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
        Schema::create('cafe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('name', 150);
            $table->unsignedBigInteger('buying_unit_id');
            $table->unsignedBigInteger('usage_unit_id');
            $table->double('conversion_value')->default(0);
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->double('stock')->default(0);
            $table->text('details')->nullable();
            $table->string('picture', 255)->nullable();
            $table->char('status', 1)->default('A');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->dateTime('modified_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('store_id')->references('id')->on('cafe_stores')->onDelete('cascade');
            $table->foreign('buying_unit_id')->references('id')->on('cafe_units')->onDelete('cascade');
            $table->foreign('usage_unit_id')->references('id')->on('cafe_units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_ingredients');
    }
};

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
        Schema::create('cafe_foods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('category_id');
            $table->string('name', 250);
            $table->string('code', 50)->nullable();
            $table->string('picture', 255)->nullable();
            $table->decimal('price', 10, 2)->default(0); // Sale Price
            $table->decimal('cost_price', 10, 2)->default(0); // Total ingredient cost
            $table->char('status', 1)->default('A');
            $table->integer('position')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->dateTime('modified_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('store_id')->references('id')->on('cafe_stores')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('cafe_categories')->onDelete('cascade');
        });

        Schema::create('cafe_food_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_id');
            $table->unsignedBigInteger('ingredient_id');
            $table->unsignedBigInteger('usage_unit_id')->nullable();
            $table->decimal('quantity', 12, 4)->default(0);
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('food_id')->references('id')->on('cafe_foods')->onDelete('cascade');
            $table->foreign('ingredient_id')->references('id')->on('cafe_ingredients')->onDelete('cascade');
            $table->foreign('usage_unit_id')->references('id')->on('cafe_units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_food_details');
        Schema::dropIfExists('cafe_foods');
    }
};

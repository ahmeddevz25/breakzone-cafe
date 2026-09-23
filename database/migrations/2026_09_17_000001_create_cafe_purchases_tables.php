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
        // 1. Cafe Purchases Table
        Schema::create('cafe_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('po_no', 100)->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->date('purchase_date');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->dateTime('modified_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('store_id')->references('id')->on('cafe_stores')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('cafe_suppliers')->onDelete('cascade');
        });

        // 2. Cafe Purchase Details Table
        Schema::create('cafe_purchase_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('ingredient_id')->nullable();
            $table->unsignedBigInteger('food_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('quantity', 12, 4)->default(0);
            $table->decimal('price', 12, 4)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('purchase_id')->references('id')->on('cafe_purchases')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('cafe_items')->onDelete('set null');
            $table->foreign('ingredient_id')->references('id')->on('cafe_ingredients')->onDelete('set null');
            $table->foreign('food_id')->references('id')->on('cafe_foods')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('cafe_units')->onDelete('set null');
        });

        // 3. Cafe Quantities Table (Store-level inventory tracking from cafe-s.sql)
        if (!Schema::hasTable('cafe_quantities')) {
            Schema::create('cafe_quantities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id');
                $table->unsignedBigInteger('item_id')->nullable();
                $table->unsignedBigInteger('ingredient_id')->nullable();
                $table->unsignedBigInteger('food_id')->nullable();
                $table->decimal('quantity', 14, 4)->default(0);
                $table->timestamps();

                $table->foreign('store_id')->references('id')->on('cafe_stores')->onDelete('cascade');
                $table->foreign('item_id')->references('id')->on('cafe_items')->onDelete('cascade');
                $table->foreign('ingredient_id')->references('id')->on('cafe_ingredients')->onDelete('cascade');
                $table->foreign('food_id')->references('id')->on('cafe_foods')->onDelete('cascade');

                $table->index(['store_id', 'item_id']);
                $table->index(['store_id', 'ingredient_id']);
                $table->index(['store_id', 'food_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_quantities');
        Schema::dropIfExists('cafe_purchase_details');
        Schema::dropIfExists('cafe_purchases');
    }
};

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
        // 1. Cafe Purchase Returns (Header)
        Schema::create('cafe_purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no', 100)->unique();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('supplier_id');
            $table->date('return_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->dateTime('modified_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('purchase_id')->references('id')->on('cafe_purchases')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('cafe_stores')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('cafe_suppliers')->onDelete('cascade');
        });

        // 2. Cafe Purchase Return Details (Line Items)
        Schema::create('cafe_purchase_return_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_return_id');
            $table->unsignedBigInteger('purchase_detail_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('ingredient_id')->nullable();
            $table->unsignedBigInteger('food_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('quantity', 12, 4)->default(0);
            $table->decimal('price', 12, 4)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('purchase_return_id')->references('id')->on('cafe_purchase_returns')->onDelete('cascade');
            $table->foreign('purchase_detail_id')->references('id')->on('cafe_purchase_details')->onDelete('set null');
            $table->foreign('item_id')->references('id')->on('cafe_items')->onDelete('set null');
            $table->foreign('ingredient_id')->references('id')->on('cafe_ingredients')->onDelete('set null');
            $table->foreign('food_id')->references('id')->on('cafe_foods')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('cafe_units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_purchase_return_details');
        Schema::dropIfExists('cafe_purchase_returns');
    }
};

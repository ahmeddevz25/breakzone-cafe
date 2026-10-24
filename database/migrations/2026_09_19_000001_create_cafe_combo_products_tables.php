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
        // 1. Cafe Combo Products Table (matches tbl_cafe_combo_products from cafe-s.sql)
        if (!Schema::hasTable('cafe_combo_products')) {
            Schema::create('cafe_combo_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->nullable();
                $table->string('name', 250);
                $table->string('code', 50)->unique();
                $table->decimal('price', 10, 2)->default(0);
                $table->string('picture', 250)->nullable();
                $table->char('status', 1)->default('A');
                $table->integer('position')->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('modified_by')->nullable();
                $table->dateTime('modified_at')->nullable();
                $table->timestamps();

                // Foreign key constraint
                $table->foreign('store_id')->references('id')->on('cafe_stores')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 2. Cafe Combo Product Details Table (matches tbl_cafe_combo_product_details from cafe-s.sql)
        if (!Schema::hasTable('cafe_combo_product_details')) {
            Schema::create('cafe_combo_product_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('combo_product_id');
                $table->unsignedBigInteger('item_id')->nullable();
                $table->unsignedBigInteger('food_id')->nullable();
                $table->decimal('quantity', 12, 4)->default(1);
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('discount', 10, 2)->default(0);
                $table->decimal('total_price', 10, 2)->default(0);
                $table->timestamps();

                // Foreign keys
                $table->foreign('combo_product_id')->references('id')->on('cafe_combo_products')->onDelete('cascade');
                $table->foreign('item_id')->references('id')->on('cafe_items')->onDelete('cascade');
                $table->foreign('food_id')->references('id')->on('cafe_foods')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_combo_product_details');
        Schema::dropIfExists('cafe_combo_products');
    }
};

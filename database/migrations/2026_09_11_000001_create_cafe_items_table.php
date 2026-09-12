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
        Schema::create('cafe_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('name', 100);
            $table->string('code', 20);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->double('stock')->default(0)->nullable(); // Minimum Stock (Required in hand)
            $table->decimal('price', 10, 2)->default(0); // Purchase Price
            $table->decimal('sale_price', 10, 2)->default(0); // Sale Price
            $table->text('details')->nullable();
            $table->string('picture', 200)->nullable();
            $table->char('status', 1)->default('A');
            $table->integer('position')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->dateTime('modified_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('store_id')->references('id')->on('cafe_stores')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('cafe_categories')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('cafe_units')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('cafe_brands')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_items');
    }
};

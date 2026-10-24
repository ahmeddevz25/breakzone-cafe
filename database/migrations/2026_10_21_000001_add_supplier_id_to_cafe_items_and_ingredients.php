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
        if (Schema::hasTable('cafe_items') && !Schema::hasColumn('cafe_items', 'supplier_id')) {
            Schema::table('cafe_items', function (Blueprint $table) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('store_id');
                $table->foreign('supplier_id')->references('id')->on('cafe_suppliers')->onDelete('set null');
            });
        }

        if (Schema::hasTable('cafe_ingredients') && !Schema::hasColumn('cafe_ingredients', 'supplier_id')) {
            Schema::table('cafe_ingredients', function (Blueprint $table) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('store_id');
                $table->foreign('supplier_id')->references('id')->on('cafe_suppliers')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cafe_items') && Schema::hasColumn('cafe_items', 'supplier_id')) {
            Schema::table('cafe_items', function (Blueprint $table) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            });
        }

        if (Schema::hasTable('cafe_ingredients') && Schema::hasColumn('cafe_ingredients', 'supplier_id')) {
            Schema::table('cafe_ingredients', function (Blueprint $table) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            });
        }
    }
};

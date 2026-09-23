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
        if (!Schema::hasColumn('cafe_foods', 'stock')) {
            Schema::table('cafe_foods', function (Blueprint $table) {
                $table->double('stock')->default(0)->after('cost_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('cafe_foods', 'stock')) {
            Schema::table('cafe_foods', function (Blueprint $table) {
                $table->dropColumn('stock');
            });
        }
    }
};

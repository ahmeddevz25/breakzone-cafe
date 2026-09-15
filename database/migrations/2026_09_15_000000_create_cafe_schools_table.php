<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('cafe_schools')) {
            Schema::create('cafe_schools', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->nullable();
                $table->string('code', 20)->nullable();
                $table->char('status', 1)->default('A')->nullable();
                $table->timestamps();
            });

            // Seed default schools
            $schools = [
                ['id' => 1, 'name' => 'DHA Campus Shared Staff', 'code' => 'DHA-SS'],
                ['id' => 2, 'name' => 'Learning Alliance Junior School -Aziz Avenue', 'code' => 'LA-JS-AA'],
                ['id' => 3, 'name' => 'Learning Alliance Junior School - Faisalabad', 'code' => 'LA-JS-FSD'],
                ['id' => 4, 'name' => 'Lahore Preschool DHA', 'code' => 'LP-DHA'],
                ['id' => 5, 'name' => 'Lahore Preschool Gulberg', 'code' => 'LP-GLB'],
                ['id' => 6, 'name' => 'Lahore Preschool Faisalabad', 'code' => 'LP-FSD'],
                ['id' => 7, 'name' => 'Head Office', 'code' => 'HO'],
                ['id' => 8, 'name' => 'Learning Alliance Junior School - DHA', 'code' => 'LA-JS-DHA'],
                ['id' => 9, 'name' => 'Learning Alliance Senior School - DHA', 'code' => 'LA-SS-DHA'],
                ['id' => 10, 'name' => 'Learning Alliance International - DHA', 'code' => 'LAI-DHA'],
                ['id' => 11, 'name' => 'Learning Alliance Senior School - Aziz Avenue', 'code' => 'LA-SS-AA'],
                ['id' => 12, 'name' => 'Learning Alliance Senior School - Faisalabad', 'code' => 'LA-SS-FSD'],
                ['id' => 13, 'name' => 'Learning Alliance Junior School - Gulberg', 'code' => 'LA-JS-GLB'],
            ];

            foreach ($schools as $school) {
                DB::table('cafe_schools')->updateOrInsert(
                    ['id' => $school['id']],
                    array_merge($school, [
                        'status' => 'A',
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_schools');
    }
};

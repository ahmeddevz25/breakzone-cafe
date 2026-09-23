<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodDetail extends Model
{
    use HasFactory;

    protected $table = 'cafe_food_details';

    protected $fillable = [
        'food_id',
        'ingredient_id',
        'usage_unit_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'total_price' => 'float',
    ];

    // Relations
    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }

    public function usageUnit()
    {
        return $this->belongsTo(Unit::class, 'usage_unit_id');
    }
}

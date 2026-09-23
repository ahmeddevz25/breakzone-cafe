<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quantity extends Model
{
    use HasFactory;

    protected $table = 'cafe_quantities';

    protected $fillable = [
        'store_id',
        'item_id',
        'ingredient_id',
        'food_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }

    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id');
    }
}

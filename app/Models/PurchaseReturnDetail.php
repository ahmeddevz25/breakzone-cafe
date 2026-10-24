<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDetail extends Model
{
    use HasFactory;

    protected $table = 'cafe_purchase_return_details';

    protected $fillable = [
        'purchase_return_id',
        'purchase_detail_id',
        'item_id',
        'ingredient_id',
        'food_id',
        'unit_id',
        'quantity',
        'price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'price' => 'decimal:4',
        'total_price' => 'decimal:2',
    ];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id');
    }

    public function purchaseDetail()
    {
        return $this->belongsTo(PurchaseDetail::class, 'purchase_detail_id');
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

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function getProductNameAttribute(): string
    {
        if ($this->item) {
            return $this->item->name;
        } elseif ($this->ingredient) {
            return $this->ingredient->name;
        } elseif ($this->food) {
            return $this->food->name;
        }
        return 'N/A';
    }

    public function getProductTypeAttribute(): string
    {
        if ($this->item_id) return 'item';
        if ($this->ingredient_id) return 'ingredient';
        if ($this->food_id) return 'food';
        return 'unknown';
    }
}

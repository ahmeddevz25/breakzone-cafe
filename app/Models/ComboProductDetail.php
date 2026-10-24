<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboProductDetail extends Model
{
    use HasFactory;

    protected $table = 'cafe_combo_product_details';

    protected $fillable = [
        'combo_product_id',
        'item_id',
        'food_id',
        'quantity',
        'price',
        'discount',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'float',
        'price' => 'float',
        'discount' => 'float',
        'total_price' => 'float',
    ];

    public function comboProduct()
    {
        return $this->belongsTo(ComboProduct::class, 'combo_product_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id');
    }

    public function getProductNameAttribute()
    {
        if ($this->item) {
            return $this->item->name;
        } elseif ($this->food) {
            return $this->food->name;
        }
        return 'N/A';
    }

    public function getProductTypeAttribute()
    {
        if ($this->item_id) {
            return 'Item';
        } elseif ($this->food_id) {
            return 'Food';
        }
        return 'Unknown';
    }

    public function getProductCodeAttribute()
    {
        if ($this->item) {
            return $this->item->code;
        } elseif ($this->food) {
            return $this->food->code;
        }
        return '';
    }
}

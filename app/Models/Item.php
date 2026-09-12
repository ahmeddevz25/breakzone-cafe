<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $table = 'cafe_items';

    protected $fillable = [
        'store_id',
        'name',
        'code',
        'category_id',
        'unit_id',
        'brand_id',
        'stock',
        'price',
        'sale_price',
        'details',
        'picture',
        'status',
        'position',
        'created_by',
        'modified_by',
        'modified_at',
    ];

    protected $casts = [
        'stock' => 'float',
        'price' => 'float',
        'sale_price' => 'float',
        'position' => 'integer',
        'modified_at' => 'datetime',
    ];

    // Relations
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    // Accessors
    public function getPurchasePriceAttribute()
    {
        return $this->attributes['price'] ?? 0;
    }

    public function getPictureUrlAttribute()
    {
        if (!empty($this->picture) && Storage::disk('public')->exists($this->picture)) {
            return asset('storage/' . $this->picture);
        }
        return null;
    }
}

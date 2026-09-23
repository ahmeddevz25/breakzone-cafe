<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Food extends Model
{
    use HasFactory;

    protected $table = 'cafe_foods';

    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'code',
        'picture',
        'price',
        'cost_price',
        'stock',
        'status',
        'position',
        'created_by',
        'modified_by',
        'modified_at',
    ];

    protected $casts = [
        'price' => 'float',
        'cost_price' => 'float',
        'stock' => 'float',
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

    public function foodDetails()
    {
        return $this->hasMany(FoodDetail::class, 'food_id');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'cafe_food_details', 'food_id', 'ingredient_id')
                    ->withPivot('usage_unit_id', 'quantity', 'unit_price', 'total_price')
                    ->withTimestamps();
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
    public function getPictureUrlAttribute()
    {
        if (!empty($this->picture) && Storage::disk('public')->exists($this->picture)) {
            return asset('storage/' . $this->picture);
        }
        return null;
    }
}

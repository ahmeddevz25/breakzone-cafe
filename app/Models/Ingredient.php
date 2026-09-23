<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'cafe_ingredients';

    protected $fillable = [
        'store_id',
        'name',
        'buying_unit_id',
        'usage_unit_id',
        'conversion_value',
        'purchase_price',
        'stock',
        'details',
        'picture',
        'status',
        'created_by',
        'modified_by',
        'modified_at',
    ];

    protected $casts = [
        'conversion_value' => 'float',
        'purchase_price' => 'float',
        'stock' => 'float',
        'modified_at' => 'datetime',
    ];

    // Relations
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function buyingUnit()
    {
        return $this->belongsTo(Unit::class, 'buying_unit_id');
    }

    public function usageUnit()
    {
        return $this->belongsTo(Unit::class, 'usage_unit_id');
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

    /**
     * Recalculate recipe costs for all foods using this ingredient
     */
    public static function updateFoodCostPrices($ingredientId, $newPurchasePrice)
    {
        $ingredient = self::find($ingredientId);
        if (!$ingredient) return;

        $conv = $ingredient->conversion_value > 0 ? $ingredient->conversion_value : 1;
        $unitPrice = floatval($newPurchasePrice) / $conv;

        $details = FoodDetail::where('ingredient_id', $ingredientId)->get();
        $affectedFoodIds = [];

        foreach ($details as $detail) {
            $lineTotal = round(floatval($detail->quantity) * $unitPrice, 2);
            $detail->update([
                'unit_price' => $unitPrice,
                'total_price' => $lineTotal,
            ]);
            $affectedFoodIds[] = $detail->food_id;
        }

        foreach (array_unique($affectedFoodIds) as $foodId) {
            $totalCost = FoodDetail::where('food_id', $foodId)->sum('total_price');
            Food::where('id', $foodId)->update(['cost_price' => $totalCost]);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ComboProduct extends Model
{
    use HasFactory;

    protected $table = 'cafe_combo_products';

    protected $fillable = [
        'store_id',
        'name',
        'code',
        'price',
        'picture',
        'status',
        'position',
        'created_by',
        'modified_by',
        'modified_at',
    ];

    protected $casts = [
        'price' => 'float',
        'position' => 'integer',
        'modified_at' => 'datetime',
    ];

    // Relations
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function details()
    {
        return $this->hasMany(ComboProductDetail::class, 'combo_product_id');
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

    public function getOriginalTotalPriceAttribute()
    {
        return (float) $this->details->sum(function ($detail) {
            return (float) ($detail->price * $detail->quantity);
        });
    }

    public function getTotalSavingsAttribute()
    {
        $original = $this->original_total_price;
        $comboPrice = (float) $this->price;
        return max(0, $original - $comboPrice);
    }

    public function getSavingsPercentAttribute()
    {
        $original = $this->original_total_price;
        if ($original > 0 && $this->total_savings > 0) {
            return round(($this->total_savings / $original) * 100);
        }
        return 0;
    }
}

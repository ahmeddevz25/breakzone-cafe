<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'cafe_units';

    protected $fillable = [
        'unit',
        'quantity',
        'position',
        'status',
    ];

    // Compatibility accessor
    public function getNameAttribute()
    {
        return $this->attributes['unit'] ?? null;
    }
}

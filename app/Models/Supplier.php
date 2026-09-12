<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'cafe_suppliers';

    protected $fillable = [
        'name',
        'company',
        'address',
        'mobile',
        'ntn_no',
        'email',
        'status',
    ];

    // Compatibility accessor
    public function getNtnAttribute()
    {
        return $this->attributes['ntn_no'] ?? null;
    }
}

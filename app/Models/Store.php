<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'cafe_stores';

    protected $fillable = [
        'company_id',
        'store',
        'printer_ip_address',
        'printer_port',
        'opening_balance',
        'status',
        'position',
    ];

    // Compatibility accessors
    public function getNameAttribute()
    {
        return $this->attributes['store'] ?? null;
    }

    public function getPrinterIpAttribute()
    {
        return $this->attributes['printer_ip_address'] ?? null;
    }
}

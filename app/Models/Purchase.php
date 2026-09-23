<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'cafe_purchases';

    protected $fillable = [
        'store_id',
        'supplier_id',
        'po_no',
        'total',
        'purchase_date',
        'school_id',
        'notes',
        'created_by',
        'modified_by',
        'modified_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'purchase_date' => 'date',
        'modified_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}

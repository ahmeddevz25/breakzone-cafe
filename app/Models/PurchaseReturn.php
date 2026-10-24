<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $table = 'cafe_purchase_returns';

    protected $fillable = [
        'return_no',
        'purchase_id',
        'store_id',
        'supplier_id',
        'return_date',
        'total_amount',
        'reason',
        'school_id',
        'created_by',
        'modified_by',
        'modified_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'return_date' => 'date',
        'modified_at' => 'datetime',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

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
        return $this->hasMany(PurchaseReturnDetail::class, 'purchase_return_id');
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

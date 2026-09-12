<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'cafe_categories';

    protected $fillable = [
        'category',
        'parent_id',
        'position',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function getFullPathAttribute()
    {
        $path = [];
        $current = $this;
        while ($current) {
            array_unshift($path, $current->category ?? $current->name);
            $current = $current->parent;
        }
        return implode(' &raquo; ', $path);
    }

    // Compatibility accessor
    public function getNameAttribute()
    {
        return $this->attributes['category'] ?? null;
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cafe_school_id',
        'store_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get list of school IDs as an array.
     */
    public function getCafeSchoolIdsAttribute(): array
    {
        if (empty($this->attributes['cafe_school_id'])) {
            return [];
        }
        $val = $this->attributes['cafe_school_id'];
        $decoded = json_decode($val, true);
        if (is_array($decoded)) {
            return array_map('intval', $decoded);
        }
        return array_values(array_filter(array_map('intval', explode(',', $val))));
    }

    /**
     * Get list of store IDs as an array.
     */
    public function getStoreIdsAttribute(): array
    {
        if (empty($this->attributes['store_id'])) {
            return [];
        }
        $val = $this->attributes['store_id'];
        $decoded = json_decode($val, true);
        if (is_array($decoded)) {
            return array_map('intval', $decoded);
        }
        return array_values(array_filter(array_map('intval', explode(',', $val))));
    }
}
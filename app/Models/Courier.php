<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'level',
        'address',
        'is_active',
        'registered_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'registered_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Courier $courier) {
            $courier->email = strtolower($courier->email);
            $courier->registered_at = $courier->registered_at ?? now();
        });

        static::updating(function (Courier $courier) {
            if ($courier->isDirty('email')) {
                $courier->email = strtolower($courier->email);
            }
        });
    }
}

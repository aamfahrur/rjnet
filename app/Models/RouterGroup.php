<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouterGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function routers(): HasMany
    {
        return $this->hasMany(Router::class);
    }
}

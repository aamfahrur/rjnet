<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    protected $table = 'login_histories';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'location',
        'status',
        'failure_reason',
        'login_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

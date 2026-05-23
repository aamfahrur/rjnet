<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObjects\NetworkSpeed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternetPackage extends Model
{
    use SoftDeletes;

    protected $table = 'internet_packages';

    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'setup_fee',
        'download_speed_bps',
        'upload_speed_bps',
        'fup_limit_bytes',
        'mikrotik_profile',
        'mikrotik_parent_queue',
        'priority',
        'burst_limit_bps',
        'burst_threshold_bps',
        'burst_time',
        'limit_bytes_in',
        'limit_bytes_out',
        'address_list',
        'ip_pool',
        'is_active',
        'is_visible',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'price'               => 'integer',
        'setup_fee'           => 'integer',
        'download_speed_bps'  => 'integer',
        'upload_speed_bps'    => 'integer',
        'fup_limit_bytes'     => 'integer',
        'priority'            => 'integer',
        'burst_limit_bps'     => 'integer',
        'burst_threshold_bps' => 'integer',
        'limit_bytes_in'      => 'integer',
        'limit_bytes_out'     => 'integer',
        'is_active'           => 'boolean',
        'is_visible'          => 'boolean',
        'sort_order'          => 'integer',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'package_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    // =========================================================================
    // Accessors & Helpers
    // =========================================================================

    public function getDownloadSpeedAttribute(): NetworkSpeed
    {
        return NetworkSpeed::fromBps($this->download_speed_bps);
    }

    public function getUploadSpeedAttribute(): NetworkSpeed
    {
        return NetworkSpeed::fromBps($this->upload_speed_bps);
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getDownloadSpeedHumanAttribute(): string
    {
        return $this->downloadSpeed->toHuman();
    }

    public function getUploadSpeedHumanAttribute(): string
    {
        return $this->uploadSpeed->toHuman();
    }

    public function hasFup(): bool
    {
        return $this->fup_limit_bytes !== null;
    }

    public function getFupLimitHumanAttribute(): string
    {
        if (!$this->hasFup()) {
            return 'Unlimited';
        }
        $gb = $this->fup_limit_bytes / (1024 * 1024 * 1024);
        return $gb >= 1 ? number_format($gb, 0) . ' GB' : number_format($this->fup_limit_bytes / (1024 * 1024), 0) . ' MB';
    }
}

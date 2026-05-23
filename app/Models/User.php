<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;

    protected string $guard_name = 'web';

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'avatar',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'two_factor_secret',
        'two_factor_enabled',
        'telegram_chat_id',
        'notification_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at'        => 'datetime',
        'password'                 => 'hashed',
        'is_active'                => 'boolean',
        'two_factor_enabled'       => 'boolean',
        'last_login_at'            => 'datetime',
        'notification_preferences' => 'array',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function ticketReplies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStaff($query)
    {
        return $query->role(['admin', 'teknisi', 'cs']);
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isTeknisi(): bool
    {
        return $this->hasRole('teknisi');
    }

    public function isCustomerService(): bool
    {
        return $this->hasRole('cs');
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function recordLogin(string $ip, string $userAgent, string $status = 'success', ?string $failureReason = null): void
    {
        $this->loginHistories()->create([
            'ip_address'     => $ip,
            'user_agent'     => $userAgent,
            'status'         => $status,
            'failure_reason' => $failureReason,
            'login_at'       => now(),
        ]);

        if ($status === 'success') {
            $this->update([
                'last_login_at' => now(),
                'last_login_ip' => $ip,
            ]);
        }
    }

    public function notificationPreference(NotificationChannel $channel): bool
    {
        return in_array($channel->value, $this->notification_preferences ?? [], true);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=605CFF&color=fff&size=128';
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasPushSubscriptions;

    
    public const MAX_FAILED_LOGIN_ATTEMPTS = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'role',
        'status_akun',
        'foto',
        'must_change_password',
        'password',
        'default_password',
        'failed_login_attempts',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];

    }

    public function guruBk()
    {
        return $this->hasOne(GuruBK::class);
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    
    public function isLoginLocked(): bool
    {
        return $this->failed_login_attempts >= self::MAX_FAILED_LOGIN_ATTEMPTS;
    }

    public function incrementFailedLoginAttempts(): void
    {
        $this->increment('failed_login_attempts');
    }
  
    public function resetFailedLoginAttempts(): void
    {
        if ($this->failed_login_attempts !== 0) {
            $this->update(['failed_login_attempts' => 0]);
        }
    }

}

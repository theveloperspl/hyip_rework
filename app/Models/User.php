<?php

namespace App\Models;

use Assada\Achievements\Achiever;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use HasFactory, Notifiable, Achiever;

    public const ACTIVE = '1';
    public const UNVERIFIED = '0';
    public const SUSPENDED = '-1';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
//        'username',
//        'email',
//        'password',
//        'upline',
//        'leader',
//        'status',
//        'second_factor',
//        'language',
//        'main_wallet'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'leader' => 'boolean',
        'second_factor' => 'boolean',
        'administrator' => 'boolean'
    ];

    /**
     * Add a mutator to ensure hashed passwords
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * Get the user's referral link.
     *
     * @return string
     */
    public function getReferralLinkAttribute(): string
    {
        return $this->referral_link = route('landing.index', ['ref' => $this->username]);
    }

    /**
     * Get email verifications data associated with the user.
     */
    public function emailVerifications(): HasMany
    {
        return $this->hasMany(EmailVerification::class, 'email', 'email');
    }

    /**
     * Get password resets data associated with the user.
     */
    public function passwordResets(): HasMany
    {
        return $this->hasMany(PasswordReset::class, 'email', 'email');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public const ACTIVE = '1';
    public const SUSPENDED = '0';
    public const DISABLED = '-1';
    public const NOT_DISABLED_CACHE_KEY = 'not-disabled-currencies';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'destination_tag' => 'boolean'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'currency_code',
        'icon'
    ];

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCodeAttribute(): string
    {
        $code = 'USD';
        if ($this->type === 'crypto') {
            $code = strtoupper($this->small);
        }

        return $code;
    }

    /**
     * Get currency icon
     *
     * @return string
     */
    public function getIconAttribute(): string
    {
        $icon = asset("images/coins/" . $this->big . ".png");
        if (!$this->big) {
            $icon = asset("images/coins/" . strtoupper($this->long) . ".png");
        }

        return $icon;
    }

    /**
     * Return currencies that are active
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query): mixed
    {
        return $query->where('enabled', self::ACTIVE);
    }

    /**
     * Return currencies that are not disabled
     *
     * @param $query
     * @return mixed
     */
    public function scopeNotDisabled($query): mixed
    {
        return $query->where('enabled', '!=', self::DISABLED);
    }
}

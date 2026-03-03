<?php

namespace App\Models;

use App\Services\SecurityService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'display_name',
        'currency',
        'balance',
        'account_number_enc',
        'account_number', // Virtual attribute for mass assignment
        'bank_name',
        'bank_code',
        'type',
    ];

    // Virtual attribute for decrypted account number
    public function getAccountNumberAttribute()
    {
        if (empty($this->account_number_enc)) {
            return null;
        }
        try {
            return app(SecurityService::class)->decryptRSA($this->account_number_enc);
        } catch (\Exception $e) {
            return 'ERROR_DECRYPT';
        }
    }

    public function setAccountNumberAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['account_number_enc'] = null;
        } else {
            $this->attributes['account_number_enc'] = app(SecurityService::class)->encryptRSA($value);
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }
}

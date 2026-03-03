<?php

namespace App\Models;

use App\Services\SecurityService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'account_id',
        'amount',
        'currency',
        'merchant',
        'method',
        'order_number',
        'fee',
        'total',
        'recipient_name',
        'recipient_account', // Virtual attribute for RSA encryption
        'recipient_account_mask',
        'recipient_account_enc',
        'note_enc',
        'status',
        'idempotency_key',
    ];

    protected $casts = [
        // 'recipient_account_enc' => 'encrypted', // We use RSA for this now
        'note_enc' => 'encrypted', // AES-256-GCM for note is fine
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Virtual attribute for decrypted recipient account
    public function getRecipientAccountAttribute()
    {
        if (empty($this->recipient_account_enc)) {
            return null;
        }
        try {
            return app(SecurityService::class)->decryptRSA($this->recipient_account_enc);
        } catch (\Exception $e) {
            return 'ERROR_DECRYPT';
        }
    }

    public function setRecipientAccountAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['recipient_account_enc'] = null;
        } else {
            $this->attributes['recipient_account_enc'] = app(SecurityService::class)->encryptRSA($value);
        }
    }

    // Allow setting raw encrypted value if needed, but usually we set recipient_account
    // However, in controller we might be passing 'recipient_account_enc' directly if we don't change controller logic.
    // The controller does: 'recipient_account_enc' => $validated['recipient_account']
    // So we need to intercept that.
    
    public function setRecipientAccountEncAttribute($value)
    {
        // If the value looks like it's already encrypted (Base64 and long), maybe keep it?
        // But for safety, and since the controller passes PLAINTEXT to this field in the current logic,
        // we should treat input to this attribute as PLAINTEXT to be encrypted, 
        // OR we change the controller to use 'recipient_account' virtual attribute.
        
        // Let's assume the controller passes plaintext to 'recipient_account_enc' based on the code I saw.
        // So we encrypt it here.
        
        // BUT wait, if we use $fillable with 'recipient_account_enc', doing $model->recipient_account_enc = 'plain' 
        // calls this mutator.
        
        // To avoid double encryption if we change controller, let's just stick to the plan:
        // The controller passes plaintext. We encrypt it.
        
        $this->attributes['recipient_account_enc'] = app(SecurityService::class)->encryptRSA($value);
    }
    
    // We also need an accessor for recipient_account_enc to return the raw value if we ever need it?
    // Laravel default accessor returns the attribute.
    // But we defined a mutator, so we don't strictly need an accessor for the raw column unless we want to modify it.

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}

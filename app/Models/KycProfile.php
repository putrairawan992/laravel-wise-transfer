<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycProfile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'document_number_enc',
        'document_file_path',
        'address_enc',
        'contact_number',
        'bank_account_file_path',
        'utility_bill_file_path',
        'face_straight_path',
        'face_left_path',
        'face_right_path',
        'face_top_path',
        'face_bottom_path',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'rejection_reason',
    ];

    protected $casts = [
        'document_number_enc' => 'encrypted',
        'address_enc' => 'encrypted',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

<?php

namespace App\Notifications;

use App\Models\KycProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KycStatusChanged extends Notification
{
    use Queueable;

    public function __construct(protected KycProfile $kyc)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->kyc->status;

        $message = match ($status) {
            'approved' => 'Your e-KYC is approved.',
            'rejected' => 'Your e-KYC is rejected.',
            default => 'Your e-KYC status changed.',
        };

        return [
            'kyc_id' => $this->kyc->id,
            'status' => $status,
            'message' => $message,
            'rejection_reason' => $this->kyc->rejection_reason,
            'reviewed_at' => $this->kyc->reviewed_at?->toISOString(),
        ];
    }
}


<?php

namespace App\Notifications;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferSent extends Notification
{
    use Queueable;

    protected $transfer;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transfer_id' => $this->transfer->id,
            'amount' => $this->transfer->amount,
            'currency' => $this->transfer->currency,
            'recipient_name' => $this->transfer->recipient_name,
            'status' => $this->transfer->status,
            'message' => 'You sent ' . $this->transfer->currency . ' ' . number_format($this->transfer->amount, 2) . ' to ' . $this->transfer->recipient_name,
        ];
    }
}

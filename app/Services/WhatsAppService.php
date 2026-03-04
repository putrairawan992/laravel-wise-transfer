<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $sid;
    protected $token;
    protected $from;
    protected $client;

    public function __construct()
    {
        $this->sid = env('TWILIO_AUTH_SID');
        $this->token = env('TWILIO_AUTH_TOKEN');
        $this->from = env('TWILIO_WHATSAPP_FROM');

        if ($this->sid && $this->token) {
            $this->client = new Client($this->sid, $this->token);
        }
    }

    public function sendMessage($to, $message)
    {
        if (!$this->client) {
            Log::error('Twilio credentials not configured.');
            return ['error' => 'Twilio not configured'];
        }

        try {
            // Twilio WhatsApp requires 'whatsapp:' prefix for numbers
            // Ensure $to has country code but no 'whatsapp:' prefix yet
            // If $to comes as '08123...', convert to '+628123...'
            
            // Basic formatting for ID numbers (example)
            if (substr($to, 0, 1) === '0') {
                $to = '+62' . substr($to, 1);
            }
            
            // Add whatsapp prefix if missing
            $recipient = str_starts_with($to, 'whatsapp:') ? $to : 'whatsapp:' . $to;
            $sender = str_starts_with($this->from, 'whatsapp:') ? $this->from : 'whatsapp:' . $this->from;

            $message = $this->client->messages->create(
                $recipient,
                [
                    'from' => $sender,
                    'body' => $message
                ]
            );

            return [
                'sid' => $message->sid,
                'status' => $message->status,
                'success' => true
            ];

        } catch (\Exception $e) {
            Log::error('Twilio WhatsApp Error: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'success' => false
            ];
        }
    }
}

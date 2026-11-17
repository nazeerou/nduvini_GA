<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NextSmsService
{
    protected string $username;
    protected string $password;
    protected string $senderId;
        // protected string $baseUrl = 'https://api.nextsms.co.tz';
    protected string $baseUrl = ' https://messaging-service.co.tz/';

    public function __construct()
    {
        $this->username = config('services.nextsms.username');
        $this->password = config('services.nextsms.password');
        $this->senderId = config('services.nextsms.sender_id');
    }

    /**
     * Send a single SMS.
     *
     * @param string $to   Phone number (e.g., 255712345678 or 0712345678)
     * @param string $text The message body.
     * @return bool
     */
    public function send(string $to, string $text): bool
    {
        try {
            // NEXT SMS endpoint for a single SMS[reference:1]
            $response = Http::withBasicAuth($this->username, $this->password)
                ->post("{$this->baseUrl}/api/sms/v1/text/single", [
                    'from' => $this->senderId, // Your "MANANI TECH" ID
                    'to'   => $to,
                    'text' => $text,
                ]);

            if (! $response->successful()) {
                Log::error('NEXT SMS sending failed', [
                    'to'       => $to,
                    'response' => $response->body(),
                ]);
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('NEXT SMS exception', [
                'to'    => $to,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    private string $appId;
    private string $apiKey;
    private string $baseUrl = 'https://onesignal.com/api/v1/notifications';

    public function __construct()
    {
        $this->appId  = config('services.onesignal.app_id');
        $this->apiKey = config('services.onesignal.api_key');
    }

    /**
     * Cek apakah user sedang online (aktif dalam 2 menit terakhir)
     */
    private function isUserOnline(string $userId): bool
    {
        $user = User::find($userId);
        if (!$user || !$user->last_seen_at) return false;
        return $user->last_seen_at->diffInSeconds(now()) < 120;
    }

    /**
     * Kirim push notification ke user — hanya jika user sedang offline
     */
    public function sendToUser(string $recipientUserId, string $title, string $body, array $data = [], ?string $url = null): void
    {
        if ($this->isUserOnline($recipientUserId)) {
            Log::info('[OneSignal] Skip — user sedang online: ' . $recipientUserId);
            return;
        }

        try {
            $payload = [
                'app_id'          => $this->appId,
                'include_aliases' => ['external_id' => [$recipientUserId]],
                'target_channel'  => 'push',
                'headings'        => ['en' => $title],
                'contents'        => ['en' => $body],
                'data'            => $data,
            ];

            // Tambahkan URL tujuan langsung di payload
            if ($url) {
                $payload['url'] = $url;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl, $payload);

            Log::info('[OneSignal] Notifikasi terkirim ke: ' . $recipientUserId, [
                'status'   => $response->status(),
                'response' => $response->json()
            ]);
        } catch (\Exception $e) {
            Log::error('[OneSignal] Error: ' . $e->getMessage());
        }
    }
}
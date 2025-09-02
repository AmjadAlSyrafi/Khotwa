<?php

namespace App\Jobs;

use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;
    public array $notification;
    public array $data;

    /**
     * @param int $userId  -> id المستخدم المستلم
     * @param array $notification -> ['title'=>'..','body'=>'..']
     * @param array $data -> بيانات إضافية
     */
    public function __construct(int $userId, array $notification, array $data = [])
    {
        $this->userId = $userId;
        $this->notification = $notification;
        $this->data = $data;
    }

    public function handle(FcmService $fcm, LoggerInterface $logger)
    {
        $tokens = DB::table('device_tokens')
            ->where('user_id', $this->userId)
            ->pluck('token');

        foreach ($tokens as $token) {
            try {
                $fcm->sendToToken($token, $this->notification, $this->data);
                // حدث last used at
                DB::table('device_tokens')->where('token', $token)->update(['last_used_at' => now(), 'updated_at' => now()]);
            } catch (\Throwable $e) {
                // سجل الخطأ واحذف التوكن لإنه غالبًا غير صالح
                $logger->warning("FCM send failed for token {$token}: " . $e->getMessage());
                DB::table('device_tokens')->where('token', $token)->delete();
            }
        }
    }
}

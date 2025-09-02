<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class FcmService
{
    private Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * إرسال رسالة إلى توكن واحد
     *
     * @param string $token
     * @param array $notification ['title' => '...', 'body' => '...', 'image' => '...'] (اختياري)
     * @param array $data مصفوفة بيانات (key => value)
     */
    public function sendToToken(string $token, array $notification = [], array $data = [])
    {
        // بناء Notification object إن وُجد نص
        $fcmNotification = null;
        if (!empty($notification['title']) || !empty($notification['body'])) {
            $fcmNotification = FcmNotification::create(
                $notification['title'] ?? null,
                $notification['body'] ?? null
            );
        }

        // ابني الرسالة
        $message = CloudMessage::withTarget('token', $token);

        if ($fcmNotification !== null) {
            $message = $message->withNotification($fcmNotification);
        }

        if (!empty($data)) {
            // قيم الـ data  لازم تكون سلاسل نصية
            $stringData = [];
            foreach ($data as $k => $v) {
                $stringData[$k] = is_string($v) ? $v : json_encode($v);
            }
            $message = $message->withData($stringData);
        }

        return $this->messaging->send($message);
    }
}

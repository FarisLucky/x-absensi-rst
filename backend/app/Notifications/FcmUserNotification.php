<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class FcmUserNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $user = auth()->user();

        Log::info('test', [$user]);

        return (new FcmMessage())
            ->setNotification((
                    new FcmNotification())
                    ->setTitle("Test Notification")
                    ->setBody("{$user->nama} Berhasil")
            )
            ->setData(['click_action' => "https://gsabsen.my.id"]);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

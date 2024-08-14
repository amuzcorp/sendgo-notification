<?php

namespace Techigh\SendgoNotification\Attributes\Alim;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Techigh\SendgoNotification\Exceptions\SendGoException;

class AlimTalkChannel
{

    protected AlimTalk $attribute;

    public function __construct(AlimTalk $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @throws SendGoException
     */
    public function send($notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toAlim')) {
            $message = $notification->toAlim($notifiable);
            $this->attribute->send($message->toArray());
        }
    }
}

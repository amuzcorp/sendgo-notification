<?php

namespace Techigh\SendgoNotification\Attributes\Sms;

use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Contracts\ChannelInterface;
use Techigh\SendgoNotification\Exceptions\SendGoException;

class SmsChannel implements ChannelInterface
{

    protected Sms $attribute;

    public function __construct(Sms $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @throws SendGoException
     */
    public function send($notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toSms')) {
            $message = $notification->toSms($notifiable);
            $this->attribute->send($message->toArray());
        }
    }
}

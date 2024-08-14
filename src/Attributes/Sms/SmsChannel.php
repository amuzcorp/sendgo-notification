<?php

namespace Techigh\SendgoNotification\Attributes\Sms;

use Exception;
use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Contracts\ChannelInterface;
use Techigh\SendgoNotification\Contracts\SendGoAttributeInterface;
use Techigh\SendgoNotification\Exceptions\SendGoException;
use Techigh\SendgoNotification\SendGo;

//implements SendGoChannelInterface
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

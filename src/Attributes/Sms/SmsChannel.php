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

            if ($message->hasMultipleRecipients()) {
                throw new SendGoException(
                    'toMany() is not supported in Laravel Notification channels. Use the Sms attribute service directly.',
                    ['error_code' => 'MULTIPLE_RECIPIENTS_NOT_SUPPORTED_IN_NOTIFICATION']
                );
            }

            $this->attribute->send($message->toArray());
        }
    }
}

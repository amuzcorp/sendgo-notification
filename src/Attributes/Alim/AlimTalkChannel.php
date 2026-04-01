<?php

namespace Techigh\SendgoNotification\Attributes\Alim;

use Illuminate\Notifications\Notification;
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

            if ($message->hasMultipleRecipients()) {
                throw new SendGoException(
                    'toMany() is not supported in Laravel Notification channels. Use the AlimTalk attribute service directly.',
                    ['error_code' => 'MULTIPLE_RECIPIENTS_NOT_SUPPORTED_IN_NOTIFICATION']
                );
            }

            $this->attribute->send($message->toArray());
        }
    }
}

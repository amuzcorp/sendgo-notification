<?php

namespace Techigh\SendgoNotification\Attributes\Friend;

use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Exceptions\SendGoException;

class FriendTalkChannel
{

    protected FriendTalk $attribute;

    public function __construct(FriendTalk $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @throws SendGoException
     */
    public function send($notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toFriend')) {
            $message = $notification->toFriend($notifiable);

            if ($message->hasMultipleRecipients()) {
                throw new SendGoException(
                    'toMany() is not supported in Laravel Notification channels. Use the FriendTalk attribute service directly.',
                    ['error_code' => 'MULTIPLE_RECIPIENTS_NOT_SUPPORTED_IN_NOTIFICATION']
                );
            }

            $this->attribute->send($message->toArray());
        }
    }
}

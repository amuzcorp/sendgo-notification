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
            $this->attribute->send($message->toArray());
        }
    }
}

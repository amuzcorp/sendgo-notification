<?php

namespace Techigh\SendgoNotification\Contracts;

use Illuminate\Notifications\Notification;

interface ChannelInterface
{

    public function send($notifiable, Notification $notification);
}

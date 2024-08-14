<?php

namespace Techigh\SendgoNotification\Contracts;

use Illuminate\Notifications\Notification;

interface ChannelInterface
{

//    public function __construct($attribute);

    public function send($notifiable, Notification $notification);


}

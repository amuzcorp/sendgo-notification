<?php

namespace Techigh\SendgoNotification;

use Illuminate\Support\ServiceProvider;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkChannel;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkChannel;
use Techigh\SendgoNotification\Attributes\Sms\SmsChannel;
use Techigh\SendgoNotification\Contracts\ChannelInterface;

class SendgoNotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/sendgo.php', 'sendgo');
        if ($this->app->runningInConsole()) {
            $this->app->bind(ChannelInterface::class, SmsChannel::class);
            $this->app->bind(ChannelInterface::class, AlimTalkChannel::class);
            $this->app->bind(ChannelInterface::class, FriendTalkChannel::class);
        }
    }

    public function boot(): void
    {
        $this->publishes([__DIR__ . '/config/sendgo.php' => config_path('sendgo.php')], 'sendgo');
    }
}

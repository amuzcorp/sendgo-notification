<?php

namespace Techigh\SendgoNotification;

use Illuminate\Support\ServiceProvider;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalk;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkChannel;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalk;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkChannel;
use Techigh\SendgoNotification\Attributes\Sms\Sms;
use Techigh\SendgoNotification\Attributes\Sms\SmsChannel;

class SendgoNotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/sendgo.php', 'sendgo');

        // Attribute 클래스들을 싱글톤으로 등록
        // 애플리케이션 생명주기 동안 한 번만 생성되어 토큰을 재사용합니다
        $this->app->singleton(Sms::class, function ($app) {
            return new Sms();
        });

        $this->app->singleton(AlimTalk::class, function ($app) {
            return new AlimTalk();
        });

        $this->app->singleton(FriendTalk::class, function ($app) {
            return new FriendTalk();
        });

        // Channel 클래스들도 싱글톤으로 등록
        $this->app->singleton(SmsChannel::class, function ($app) {
            return new SmsChannel($app->make(Sms::class));
        });

        $this->app->singleton(AlimTalkChannel::class, function ($app) {
            return new AlimTalkChannel($app->make(AlimTalk::class));
        });

        $this->app->singleton(FriendTalkChannel::class, function ($app) {
            return new FriendTalkChannel($app->make(FriendTalk::class));
        });
    }

    public function boot(): void
    {
        $this->publishes([__DIR__ . '/config/sendgo.php' => config_path('sendgo.php')], 'sendgo');
    }
}

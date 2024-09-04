# FriendTalk

```shell
$ php artisan make:notification SendGoNotification
```

#### 1) Request to send a FriendTalk for Single Phone Number

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Techigh\SendgoNotification\Attributes\Sms\SmsMessage;
use Techigh\SendgoNotification\Attributes\Sms\SmsChannel;

class SendGoNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [SmsChannel::class];
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toSms(object $notifiable): SmsMessage
    {
        return SmsMessage::make()
            ->campaignType('MESSAGE')
            ->messageType('SMS')
            ->scheduleType('DIRECTLY')
            ->content('Welcome SendGo Sms')
            ->to($notifiable->phone)
            ->at();
    }
}
```

```php
use App\Models\User;
$user = User::query()->first();
$user->notify(new SendGoNotification());
```

#### 2) Request to send a SMS for Single Contact

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Techigh\SendgoNotification\Attributes\Sms\SmsMessage;
use Techigh\SendgoNotification\Attributes\Sms\SmsChannel;

class SendGoNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [SmsChannel::class];
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toSms(object $notifiable): SmsMessage
    {
        return SmsMessage::make()
            ->campaignType('MESSAGE')
            ->messageType('SMS')
            ->scheduleType('DIRECTLY')
            ->content('Welcome SendGo Sms')
            ->to([
            'contact' => $notifiable->phone, 
            'name' => $notifiable->name, 
            'var1' => $notifiable->variable1,
            'var2' => $notifiable->variable2,
            'var3' => $notifiable->variable3,
            'var4' => $notifiable->variable4,
            'var5' => $notifiable->variable5,
            ])
            ->at();
    }
}
```

```php
use App\Models\User;
$user = User::query()->first();
$user->notify(new SendGoNotification());
```

# AlimTalk

```shell
php artisan make:notification SendGoNotification
```

#### 1) Request to send a SMS for Contact

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkMessage;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkChannel;

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
        return [AlimTalkChannel::class];
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toAlim(object $notifiable): AlimTalkMessage
    {
        return AlimTalkMessage::make()
            ->templateCode('XX-XXXX') // REQUIRED
            ->scheduleType('DIRECTLY') // OPTIONAL, default = 'DIRECTLY'
            ->replaceSms('N') // OPTIONAL, default = 'N', replace sms when failed to send
            ->smsTitle('Title for AlimTalk') // OPTIONAL, default = null, REQUIRED when replace sms is 'Y'  
            ->smsContent('Content for AlimTalk') // OPTIONAL, default = null, REQUIRED when replace sms is 'Y'
            ->to([
                'contact' => $notifiable->phone, 
                'name' => $notifiable->name, 
                'var1' => $notifiable->variable1,
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

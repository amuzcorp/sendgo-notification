# SMS

```shell
php artisan make:notification SendGoNotification
```

```php
use App\Models\User;
$user = User::query()->first();
$user->notify(new SendGoNotification());
```

#### 1) Request to send a SMS for Contact

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
            ->campaignType('MESSAGE') // REQUIRED, default = 'MESSAGE'
            ->messageType('SMS') // REQUIRED, 'SMS' | 'LMS' | 'MMS', default = 'SMS'
            ->scheduleType('DIRECTLY') // OPTIONAL, 'DIRECTLY' | 'SCHEDULED', default = 'DIRECTLY'
            ->content('Welcome SendGo Sms') // REQUIRED
            ->to([
                'contact' => $notifiable->phone, // REQUIRED
                'name' => $notifiable->name, // OPTIONAL
                'var1' => $notifiable->variable1, // OPTIONAL   
                'var2' => $notifiable->variable2, // OPTIONAL
                'var3' => $notifiable->variable3, // OPTIONAL
                'var4' => $notifiable->variable4, // OPTIONAL
                'var5' => $notifiable->variable5, // OPTIONAL
                'var6' => $notifiable->variable6, // OPTIONAL
                'var7' => $notifiable->variable7, // OPTIONAL
                'var8' => $notifiable->variable8, // OPTIONAL
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

### 2) LMS, MMS

```php

    public function toSms(object $notifiable): SmsMessage
    {
        return SmsMessage::make()
            ->campaignType('MESSAGE')
            ->messageType('LMS') 
            ->content('Welcome SendGo LMS or MMS') // REQUIRED
            ->subject('Subject for LMS or MMS') // REQUIRED
            ->files([ // REQUIRED for MMS, max 3
               $notifiable->file1, 
               $notifiable->file2,    
               $notifiable->file3, 
            ])
            ->to([
                'contact' => $notifiable->phone, 
                'name' => $notifiable->name,
                'var1' => $notifiable->variable1,  
            ])
            ->at();
    }

```

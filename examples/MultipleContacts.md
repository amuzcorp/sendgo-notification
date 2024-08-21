# Multiple Contacts

#### 1) Request to send a SMS for Contacts

```shell
$ php artisan make:notification SmsNotification
```

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Techigh\SendgoNotification\Attributes\Sms\SmsMessage;
use Techigh\SendgoNotification\Attributes\Sms\SmsChannel;

class SmsNotification extends Notification
{
    use Queueable;
    
    private array $contacts;

    /**
     * Create a new notification instance.
     */
    public function __construct($contacts)
    {
        $this->contacts = $contacts;
    }
    
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
            ->to($this->contacts)
            ->at();
    }
}
```

```php
use App\Models\User;
    $contacts = [
        [
            'contact' => '01012341324',
            'name' => 'name1',
            'var1' => 'variable1',
            'var2' => 'variable2',
            'var3' => 'variable3',
            'var4' => 'variable4',
            'var5' => 'variable5',
        ], [
            'contact' => '01098769876',
            'name' => 'name2',
            'var1' => 'variable1',
            'var2' => 'variable2',
            'var3' => 'variable3',
            'var4' => 'variable4',
            'var5' => 'variable5',
        ]
    ];
$user = User::query()->first();
$user->notify(new SmsNotification($contacts));
```

#### 2) Request to send a Alim for Contacts

```shell
$ php artisan make:notification AlimNotification
```

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkChannel;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkMessage;

class AlimNotification extends Notification
{
    use Queueable;
    
    private array $contacts;

    /**
     * Create a new notification instance.
     */
    public function __construct($contacts)
    {
        $this->contacts = $contacts;
    }
    
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
        return SmsMessage::make()
            ->templateCode('your template code')
            ->to($this->contacts)
            ->at();
    }
}
```

```php
use App\Models\User;
    $contacts = [
        [
            'contact' => '01012341324',
            'name' => 'name1',
            'var1' => 'variable1',
            'var2' => 'variable2',
            'var3' => 'variable3',
            'var4' => 'variable4',
            'var5' => 'variable5',
        ], [
            'contact' => '01098769876',
            'name' => 'name2',
            'var1' => 'variable1',
            'var2' => 'variable2',
            'var3' => 'variable3',
            'var4' => 'variable4',
            'var5' => 'variable5',
        ]
    ];
$user = User::query()->first();
$user->notify(new AlimNotification($contacts));
```

#### 3) Request to send a Friend for Contacts

```shell
$ php artisan make:notification FriendNotification
```

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkChannel;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkMessage;

class FriendNotification extends Notification
{
    use Queueable;
    
    private array $contacts;

    /**
     * Create a new notification instance.
     */
    public function __construct($contacts)
    {
        $this->contacts = $contacts;
    }
    
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
    public function toFriend(object $notifiable): FriendTalkMessage
    {
        return SmsMessage::make()
            ->messageType('FT')
            ->content('Welcome SendGo Friend')
            ->to($this->contacts)
            ->at();
    }
}
```

```php
use App\Models\User;
    $contacts = [
        [
            'contact' => '01012341324',
            'name' => 'name1',
            'var1' => 'variable1',
            'var2' => 'variable2',
            'var3' => 'variable3',
            'var4' => 'variable4',
            'var5' => 'variable5',
        ], [
            'contact' => '01098769876',
            'name' => 'name2',
            'var1' => 'variable1',
            'var2' => 'variable2',
            'var3' => 'variable3',
            'var4' => 'variable4',
            'var5' => 'variable5',
        ]
    ];
$user = User::query()->first();
$user->notify(new FriendNotification($contacts));
```

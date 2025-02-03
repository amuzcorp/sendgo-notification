# SMS

이 패키지는 Laravel의 Notification 시스템을 이용하여 SMS, LMS, MMS 메시지를 전송할 수 있도록 도와줍니다.
아래 가이드를 참고하여 패키지를 설정하고 메시지를 보낼 수 있습니다.

먼저 Laravel의 `make:notification` 명령어를 사용하여 Notification 클래스를 생성합니다.

```shell
php artisan make:notification SendGoNotification
```

```php
use App\Models\User;
$user = User::query()->first();
$user->notify(new SendGoNotification());
```

위 코드를 실행하면 사용자의 휴대폰 번호로 문자가 전송됩니다.

#### 1) Request to send a SMS for Contact

- SendGoNotificdation 클래스 내부에서 toSms() 메서드를 정의하여 메시지를 설정할 수 있습니다.
    - campaignType → 기본값은 'MESSAGE'
    - messageType → 'SMS', 'LMS', 'MMS' 중 선택
    - scheduleType → 'DIRECTLY' (즉시 발송) 또는 'SCHEDULED' (예약 발송)
    - content → 메시지 내용 (필수)
    - subject → LMS/MMS 발송 시 필수
    - files → MMS 발송 시 필수 (최대 3개 파일 가능)
    - to → 수신자 정보 (전화번호, 이름, 추가 변수 포함 가능)

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

- LMS와 MMS를 발송하기 위해서는content 와 subject 값이 필수입니다.
- MMS를 발송할 때는 files 값이 필수이며 최대 3개까지 가능합니다.

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

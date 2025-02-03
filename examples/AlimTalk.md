# AlimTalk

이 패키지는 Laravel의 Notification 시스템을 이용하여 카카오톡 알림톡을 전송할 수 있도록 도와줍니다.
아래 가이드를 참고하여 패키지를 설정하고 메시지를 보낼 수 있습니다.

먼저 Laravel의 `make:notification` 명령어를 사용하여 Notification 클래스를 생성합니다.

```shell
php artisan make:notification SendGoNotification
```

#### Request to send a Alim Talk for Contact

- SendGoNotification 클래스 내부에서 toAlim() 메서드를 정의하여 메시지를 설정할 수 있습니다.
    - template → 샌드고 홈페이지에서 승인된 알림톡 템플릿 코드 (필수)
    - scheduleType → 'DIRECTLY' (즉시 발송) 또는 'SCHEDULED' (예약 발송)
    - replaceSms -> 대체 문자 발송 여부 (옵션)
    - smsTitle → 대체 문자 메시지 제목, 대체 문자 발송시 필수
    - smsContent → 대체 문자 메시지 내용, 대체 문자 발송시 필수
    - to → 수신자 정보 (전화번호, 이름, 추가 변수 포함 가능)

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

위 코드를 실행하면 사용자의 휴대폰 번호로 카카오 알림톡이 전송됩니다.

# FriendTalk

이 패키지는 Laravel의 Notification 시스템을 이용하여 카카오톡 친구톡을 전송할 수 있도록 도와줍니다.
아래 가이드를 참고하여 패키지를 설정하고 메시지를 보낼 수 있습니다.

먼저 Laravel의 `make:notification` 명령어를 사용하여 Notification 클래스를 생성합니다.

```shell
php artisan make:notification SendGoNotification
```

#### Request to send a Friend Talk for Contact

- SendGoNotification 클래스 내부에서 toFriend() 메서드를 정의하여 메시지를 설정할 수 있습니다.
    - scheduleType → 'DIRECTLY' (즉시 발송) 또는 'SCHEDULED' (예약 발송)
    - messageType → 'FT' (텍스트), 'FI' (이미지), 'FW' (와이드 이미지), 'FL' (와이드 아이템 리스트), 'FM' (커머스), 'FC' (캐러셀 피드), 'FA' (캐러셀
      커머스), 'FP' (프리미엄 동영상) 중 하나
    - content -> 친구톡 메시지 내용 (필수)
    - image -> 친구톡 메시지 내용 중 이미지, messageType 이 'FI', 'FW', 'FL', 'FM' (옵션), file 형태
    - imageUrl -> 친구톡 메시지 내용 중 이미지 url (옵션), string 형태
    - imageLink -> 친구톡 메시지 내용 중 이미지 클릭시 이동할 링크값 (옵션), string 형태
    - buttons -> 친구톡 메시지 내용 중 버튼들의 정보에 대한 배열값 (옵션)
    - wide -> 친구톡 메시지 중 와이드 이미지 여부 값, N 또는 Y 중 하나 (옵션)
    - adFlag -> 친구톡 광고성 메시지 필수 표기 사항 노출 여부, N 또는 Y 중 하나 (옵션)
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
        return [FriendTalkChannel::class];
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toFriend(object $notifiable): AlimTalkMessage
    {
        return FriendTalkMessage::make()
            ->scheduleType('DIRECTLY') // OPTIONAL, default = 'DIRECTLY'
            ->messageType('FT') // REQUIRED
            ->content('This is content') // REQUIRED
            ->image('file:/')  // OPTIONAL
            ->imageUrl('www.XXXX.XXXX/XXXXX') // OPTIONAL
            ->imageLink('www.XXXX.XXXX/XXXXX') // OPTIONAL
            ->buttons([{}, {}]) // OPTIONAL
            ->wide('N') // OPTIONAL, default = 'N', REQUIRED when message type is 'FW' 
            ->adFlag('N') // OPTIONAL, default = 'N'
            ->replaceSms('N') // OPTIONAL, default = 'N', replace sms when failed to send
            ->smsTitle('Title for FriendTalk') // OPTIONAL, default = null, REQUIRED when replace sms is 'Y'
            ->smsContent('Content for FriendTalk') // OPTIONAL, default = null, REQUIRED when replace sms is 'Y'
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

위 코드를 실행하면 사용자의 휴대폰 번호로 카카오 친구톡이 전송됩니다.

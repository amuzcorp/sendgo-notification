# SMS / LMS / MMS 예제

SMS, LMS, MMS 전송 예제 모음입니다.

---

## 기본 설정

```bash
php artisan make:notification SendGoNotification
```

`.env` 설정:

```env
SENDGO_URL=https://api.sendgo.io
SENDGO_ACCESS_KEY=your_access_key
SENDGO_SECRET_KEY=your_secret_key
SENDGO_SENDER_KEY=your_sms_sender_key

# v2 API 사용 시
SENDGO_API_VERSION=v2
```

---

## 메시지 타입

| 타입 | 설명 | 최대 길이 |
|------|------|----------|
| `SMS` | 단문 | 90바이트 (한글 45자) |
| `LMS` | 장문 | 2,000바이트 (한글 1,000자) |
| `MMS` | 멀티미디어 | 이미지 최대 3개 첨부 |

---

## 1. SMS 기본 발송

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Attributes\Sms\SmsChannel;
use Techigh\SendgoNotification\Attributes\Sms\SmsMessage;

class VerificationNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code) {}

    public function via(object $notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms(object $notifiable): SmsMessage
    {
        return SmsMessage::make()
            ->campaignType('MESSAGE')              // 기본값
            ->messageType('SMS')
            ->scheduleType('DIRECTLY')             // 즉시 발송
            ->content("[인증번호] {$this->code}\n3분 내에 입력해주세요.")
            ->to([
                'contact' => $notifiable->phone,   // 수신자 전화번호 (필수)
                'name'    => $notifiable->name,    // 수신자 이름 (선택)
            ])
            ->at();
    }
}
```

```php
$user->notify(new VerificationNotification('123456'));
```

---

## 1-1. 다건 수신자 발송

`toMany()`는 Laravel Notification 채널이 아니라 `Sms` 서비스를 직접 호출할 때 사용합니다.

```php
use Techigh\SendgoNotification\Attributes\Sms\Sms;
use Techigh\SendgoNotification\Attributes\Sms\SmsMessage;

app(Sms::class)->send(
    SmsMessage::make()
        ->messageType('SMS')
        ->content('[공지] 시스템 점검이 예정되어 있습니다.')
        ->toMany([
            [
                'contact' => '01066443892',
                'name' => 'John Doe',
                'var1' => 'group-a',
            ],
            [
                'contact' => '01012345678',
                'name' => 'Jane Doe',
                'var1' => 'group-b',
            ],
        ])
        ->at()
        ->toArray()
);
```

---

## 2. LMS 장문 발송

LMS는 `subject`가 필수입니다.

```php
public function toSms(object $notifiable): SmsMessage
{
    return SmsMessage::make()
        ->messageType('LMS')
        ->subject('[서비스 점검 안내]')             // LMS 제목 (필수)
        ->content(
            "안녕하세요, {$notifiable->name}님.\n\n" .
            "아래와 같이 서비스 점검이 예정되어 있습니다.\n\n" .
            "■ 점검 일시: 2026-04-01 02:00 ~ 06:00\n" .
            "■ 점검 내용: 시스템 업그레이드\n" .
            "■ 영향 범위: 전체 서비스\n\n" .
            "점검 시간 동안 서비스 이용이 불가하오니 양해 부탁드립니다.\n\n" .
            "감사합니다."
        )
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
        ])
        ->at();
}
```

---

## 3. MMS 이미지 첨부 발송

MMS는 `subject`와 `files`가 필수입니다. 파일은 최대 3개까지 첨부할 수 있습니다.

```php
public function toSms(object $notifiable): SmsMessage
{
    return SmsMessage::make()
        ->messageType('MMS')
        ->subject('[이벤트 안내]')                  // MMS 제목 (필수)
        ->content("봄맞이 특별 이벤트를 소개합니다!\n자세한 내용은 이미지를 확인해주세요.")
        ->files([                                   // 파일 절대 경로 (최대 3개)
            storage_path('app/public/event_01.jpg'),
            storage_path('app/public/event_02.jpg'),
        ])
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
        ])
        ->at();
}
```

---

## 4. 예약 발송

```php
public function toSms(object $notifiable): SmsMessage
{
    return SmsMessage::make()
        ->messageType('SMS')
        ->scheduleType('SCHEDULED')
        ->content("[리마인더] 내일 오전 10시 예약이 있습니다.")
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
        ])
        ->at('2026-04-01 09:00:00');               // 예약 시각 (Y-m-d H:i:s)
}
```

---

## 5. 템플릿 변수 활용 (var1 ~ var8)

```php
public function toSms(object $notifiable): SmsMessage
{
    return SmsMessage::make()
        ->messageType('SMS')
        ->content("주문 #{$this->orderNumber} 배송이 시작되었습니다.")
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
            'var1'    => $this->orderNumber,
            'var2'    => $this->trackingNumber,
            'var3'    => $this->courierName,
            'var4'    => $this->expectedDate,
        ])
        ->at();
}
```

---

## 6. 예외 처리

```php
use Techigh\SendgoNotification\Exceptions\SendGoException;

try {
    $user->notify(new VerificationNotification($code));
} catch (SendGoException $e) {
    $ctx = $e->context();

    logger()->error('SMS 발송 실패', [
        'error_code'  => $ctx['error_code'] ?? null,
        'status'      => $ctx['status'] ?? null,
        'endpoint'    => $ctx['endpoint'] ?? null,
        'api_version' => $ctx['api_version'] ?? null,
        'message'     => $e->getMessage(),
    ]);

    match ($ctx['error_code'] ?? null) {
        'INVALID_SENDER_KEY' => report('SMS 발신키를 확인하세요.'),
        'PAYMENT_REQUIRED'   => report('크레딧이 부족합니다.'),
        'EMPTY_CONTACTS'     => report('수신자 정보를 확인하세요.'),
        default              => null,
    };
}
```

---

## 7. 큐(Queue) 비동기 발송

```php
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';
    public int $tries = 3;
    public int $backoff = 5;
}
```

```php
// 대량 발송
use Illuminate\Support\Facades\Notification;

$users = User::where('status', 'active')->get();
Notification::send($users, new PromotionSmsNotification($message));
```

```bash
# 전용 큐 워커 실행
php artisan queue:work --queue=notifications
```

---

## 8. v2 API 사용

`.env`에서 `SENDGO_API_VERSION=v2`만 설정하면 코드 변경 없이 v2로 동작합니다.

```env
SENDGO_API_VERSION=v2
```

v2 에러 코드 전체 목록 → [API_V2_ERROR_CODES.md](../API_V2_ERROR_CODES.md)

---

## 9. 실전 시나리오: 인증번호 SMS

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Attributes\Sms\SmsChannel;
use Techigh\SendgoNotification\Attributes\Sms\SmsMessage;
use Techigh\SendgoNotification\Exceptions\SendGoException;

class PhoneVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(private readonly string $code) {}

    public function via(object $notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms(object $notifiable): SmsMessage
    {
        return SmsMessage::make()
            ->messageType('SMS')
            ->content("[인증번호] {$this->code} (3분 내 입력)")
            ->to([
                'contact' => $notifiable->phone,
            ])
            ->at();
    }

    public function failed(SendGoException $e): void
    {
        logger()->critical('인증번호 SMS 발송 최종 실패', [
            'user_id'    => $this->notifiable?->id,
            'error_code' => $e->context()['error_code'] ?? null,
            'message'    => $e->getMessage(),
        ]);
    }
}
```

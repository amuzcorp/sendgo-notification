# AlimTalk 예제

카카오톡 알림톡 전송 예제 모음입니다.

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
SENDGO_KAKAO_SENDER_KEY=your_kakao_sender_key

# v2 API 사용 시
SENDGO_API_VERSION=v2
```

---

## 1. 기본 즉시 발송

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkChannel;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkMessage;

class OrderConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $orderNumber) {}

    public function via(object $notifiable): array
    {
        return [AlimTalkChannel::class];
    }

    public function toAlim(object $notifiable): AlimTalkMessage
    {
        return AlimTalkMessage::make()
            ->templateCode('ORDER_CONFIRM_001')    // SendGo에서 승인받은 템플릿 코드
            ->scheduleType('DIRECTLY')             // 즉시 발송 (기본값)
            ->to([
                'contact' => $notifiable->phone,   // 수신자 전화번호 (필수)
                'name'    => $notifiable->name,    // 수신자 이름 (선택)
                'var1'    => $this->orderNumber,   // 템플릿 변수
            ])
            ->at();
    }
}
```

```php
$user->notify(new OrderConfirmedNotification('ORD-20260101-001'));
```

---

## 1-1. 다건 수신자 발송

`toMany()`는 Laravel Notification 채널이 아니라 `AlimTalk` 서비스를 직접 호출할 때 사용합니다.

```php
use Techigh\SendgoNotification\Attributes\Alim\AlimTalk;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkMessage;

app(AlimTalk::class)->send(
    AlimTalkMessage::make()
        ->templateCode('ORDER_CONFIRM_001')
        ->toMany([
            [
                'contact' => '01066443892',
                'name' => 'John Doe',
                'var1' => 'ORD-20260101-001',
            ],
            [
                'contact' => '01012345678',
                'name' => 'Jane Doe',
                'var1' => 'ORD-20260101-002',
            ],
        ])
        ->at()
        ->toArray()
);
```

---

## 2. 대체 SMS 발송 (알림톡 실패 시 자동 SMS)

```php
public function toAlim(object $notifiable): AlimTalkMessage
{
    return AlimTalkMessage::make()
        ->templateCode('DELIVERY_START_001')
        ->scheduleType('DIRECTLY')
        ->replaceSms('Y')                          // 알림톡 실패 시 SMS 대체 발송
        ->smsTitle('[배송 시작 안내]')              // 대체 SMS 제목 (replaceSms Y일 때 필수)
        ->smsContent("주문하신 상품이 출고되었습니다.\n송장번호: {$this->trackingNumber}")
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
            'var1'    => $this->orderNumber,
            'var2'    => $this->trackingNumber,
        ])
        ->at();
}
```

---

## 3. 예약 발송

```php
public function toAlim(object $notifiable): AlimTalkMessage
{
    return AlimTalkMessage::make()
        ->templateCode('PROMO_001')
        ->scheduleType('SCHEDULED')                // 예약 발송
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
            'var1'    => '봄맞이 30% 할인',
        ])
        ->at('2026-04-01 09:00:00');               // 예약 시각 (Y-m-d H:i:s)
}
```

---

## 4. 템플릿 변수 최대 활용 (var1 ~ var8)

```php
public function toAlim(object $notifiable): AlimTalkMessage
{
    return AlimTalkMessage::make()
        ->templateCode('ORDER_DETAIL_001')
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
            'var1'    => $this->order->order_number,    // 주문번호
            'var2'    => $this->order->product_name,    // 상품명
            'var3'    => number_format($this->order->price) . '원',
            'var4'    => $this->order->quantity . '개',
            'var5'    => $this->order->delivery_address,
            'var6'    => $this->order->expected_date,
            'var7'    => $this->order->tracking_number,
            'var8'    => $this->order->courier_name,
        ])
        ->at();
}
```

---

## 5. 예외 처리

```php
use Techigh\SendgoNotification\Exceptions\SendGoException;

try {
    $user->notify(new OrderConfirmedNotification($order->number));
} catch (SendGoException $e) {
    $ctx = $e->context();

    logger()->error('알림톡 발송 실패', [
        'error_code'  => $ctx['error_code'] ?? null,
        'status'      => $ctx['status'] ?? null,
        'endpoint'    => $ctx['endpoint'] ?? null,
        'api_version' => $ctx['api_version'] ?? null,
        'message'     => $e->getMessage(),
    ]);

    // 에러 코드별 분기 처리
    match ($ctx['error_code'] ?? null) {
        'INVALID_ACCESS_KEY',
        'INVALID_SECRET_KEY'      => report('SendGo 인증키를 확인하세요.'),
        'ACCESS_KEY_NOT_APPROVED' => report('SendGo 앱이 승인되지 않았습니다.'),
        'INVALID_TEMPLATE_CODE'   => report('템플릿 코드를 확인하세요.'),
        'PAYMENT_REQUIRED'        => report('크레딧이 부족합니다.'),
        'EMPTY_CONTACTS'          => report('수신자 정보를 확인하세요.'),
        default                   => null,
    };
}
```

---

## 6. 큐(Queue) 비동기 발송

```php
// Notification 클래스에 ShouldQueue 구현
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';    // 전용 큐 지정
    public int $tries = 3;                     // 최대 재시도 횟수
    public int $backoff = 10;                  // 재시도 대기 시간 (초)
}
```

```bash
# 큐 워커 실행
php artisan queue:work --queue=notifications
```

```php
// 대량 발송
use Illuminate\Support\Facades\Notification;

$users = User::where('status', 'active')->get();
Notification::send($users, new OrderConfirmedNotification($orderNumber));
```

---

## 7. v2 API 사용

v2는 `.env`에서 `SENDGO_API_VERSION=v2`만 설정하면 됩니다. Notification 코드 변경 없이 동작합니다.

```env
SENDGO_API_VERSION=v2
```

v2에서 토큰 관련 에러는 패키지가 자동으로 1회 재발급 후 재시도합니다.
단, 아래 코드들은 재시도 없이 즉시 실패합니다:

- `INVALID_ACCESS_KEY` / `INVALID_SECRET_KEY` — 잘못된 인증키
- `ACCESS_KEY_NOT_APPROVED` — 미승인 앱
- `IP_NOT_ALLOWED` — IP 차단
- `INVALID_SENDER_KEY` / `INVALID_KAKAO_SENDER_KEY` — 잘못된 발신키

v2 에러 코드 전체 목록 → [API_V2_ERROR_CODES.md](../API_V2_ERROR_CODES.md)

---

## 8. 조건부 채널 선택

```php
public function via(object $notifiable): array
{
    // 카카오톡 미사용 회원은 SMS 채널로 전환
    if (!$notifiable->uses_kakao) {
        return [\Techigh\SendgoNotification\Attributes\Sms\SmsChannel::class];
    }

    return [AlimTalkChannel::class];
}
```

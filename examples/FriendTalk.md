# FriendTalk 예제

카카오톡 친구톡 전송 예제 모음입니다.

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

## 메시지 타입

| 타입 | 설명 |
|------|------|
| `FT` | 텍스트형 |
| `FI` | 이미지형 |
| `FW` | 와이드 이미지형 |
| `FL` | 와이드 아이템 리스트형 |
| `FM` | 커머스형 |
| `FC` | 캐러셀 피드형 |
| `FA` | 캐러셀 커머스형 |
| `FP` | 프리미엄 동영상형 |

---

## 1. 기본 텍스트형 (FT)

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkChannel;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkMessage;

class EventNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return [FriendTalkChannel::class];
    }

    public function toFriend(object $notifiable): FriendTalkMessage
    {
        return FriendTalkMessage::make()
            ->scheduleType('DIRECTLY')
            ->messageType('FT')                    // 텍스트형
            ->content("안녕하세요, {$notifiable->name}님!\n\n새로운 이벤트가 시작되었습니다.")
            ->to([
                'contact' => $notifiable->phone,
                'name'    => $notifiable->name,
            ])
            ->at();
    }
}
```

```php
$user->notify(new EventNotification());
```

---

## 1-1. 다건 수신자 발송

`toMany()`는 Laravel Notification 채널이 아니라 `FriendTalk` 서비스를 직접 호출할 때 사용합니다.

```php
use Techigh\SendgoNotification\Attributes\Friend\FriendTalk;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkMessage;

app(FriendTalk::class)->send(
    FriendTalkMessage::make()
        ->messageType('FT')
        ->content('안녕하세요. 오늘의 혜택을 확인해보세요.')
        ->toMany([
            [
                'contact' => '01066443892',
                'name' => 'John Doe',
            ],
            [
                'contact' => '01012345678',
                'name' => 'Jane Doe',
            ],
        ])
        ->at()
        ->toArray()
);
```

---

## 2. 이미지형 + 버튼 (FI)

```php
public function toFriend(object $notifiable): FriendTalkMessage
{
    return FriendTalkMessage::make()
        ->scheduleType('DIRECTLY')
        ->messageType('FI')                        // 이미지형
        ->content("🎉 신상품 출시!\n\n{$this->product->name}\n가격: " . number_format($this->product->price) . "원")
        ->imageUrl($this->product->image_url)      // 이미지 URL
        ->imageLink($this->product->detail_url)    // 이미지 클릭 링크
        ->buttons([
            [
                'type'   => 'WL',                  // 웹 링크
                'name'   => '자세히 보기',
                'linkMo' => $this->product->detail_url,
                'linkPc' => $this->product->detail_url,
            ],
            [
                'type'   => 'WL',
                'name'   => '지금 구매하기',
                'linkMo' => $this->product->purchase_url,
                'linkPc' => $this->product->purchase_url,
            ],
        ])
        ->wide('N')
        ->adFlag('N')
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
        ])
        ->at();
}
```

---

## 3. 와이드 이미지형 (FW)

```php
public function toFriend(object $notifiable): FriendTalkMessage
{
    return FriendTalkMessage::make()
        ->messageType('FW')                        // 와이드 이미지형
        ->content('이번 주말 특별 세일!')
        ->imageUrl('https://cdn.example.com/banner/sale.jpg')
        ->imageLink('https://example.com/sale')
        ->wide('Y')                                // FW는 Y 필수
        ->buttons([
            [
                'type'   => 'WL',
                'name'   => '세일 보러가기',
                'linkMo' => 'https://example.com/sale',
                'linkPc' => 'https://example.com/sale',
            ],
        ])
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
        ])
        ->at();
}
```

---

## 4. 광고성 메시지 (adFlag Y)

광고성 메시지는 반드시 `adFlag('Y')`를 설정해야 합니다.

```php
public function toFriend(object $notifiable): FriendTalkMessage
{
    return FriendTalkMessage::make()
        ->messageType('FT')
        ->content("(광고) 오늘만! 전 상품 20% 할인\n무료수신거부 080-000-0000")
        ->adFlag('Y')                              // 광고성 메시지 표기
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
        ])
        ->at();
}
```

---

## 5. 대체 SMS 발송 (친구톡 실패 시)

```php
public function toFriend(object $notifiable): FriendTalkMessage
{
    return FriendTalkMessage::make()
        ->messageType('FT')
        ->content("주문이 완료되었습니다!\n주문번호: {$this->orderNumber}")
        ->replaceSms('Y')
        ->smsTitle('[주문 완료]')
        ->smsContent("주문이 완료되었습니다. 주문번호: {$this->orderNumber}")
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
        ])
        ->at();
}
```

---

## 6. 예약 발송

```php
public function toFriend(object $notifiable): FriendTalkMessage
{
    return FriendTalkMessage::make()
        ->messageType('FT')
        ->content("내일 오전 10시, 라이브 방송이 시작됩니다!")
        ->scheduleType('SCHEDULED')
        ->to([
            'contact' => $notifiable->phone,
            'name'    => $notifiable->name,
        ])
        ->at('2026-04-01 09:00:00');               // 방송 1시간 전 예약
}
```

---

## 7. 예외 처리

```php
use Techigh\SendgoNotification\Exceptions\SendGoException;

try {
    $user->notify(new EventNotification());
} catch (SendGoException $e) {
    $ctx = $e->context();

    logger()->error('친구톡 발송 실패', [
        'error_code'  => $ctx['error_code'] ?? null,
        'status'      => $ctx['status'] ?? null,
        'api_version' => $ctx['api_version'] ?? null,
        'message'     => $e->getMessage(),
    ]);
}
```

---

## 8. 큐(Queue) 비동기 발송

```php
use Illuminate\Contracts\Queue\ShouldQueue;

class EventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';
    public int $tries = 3;
    public int $backoff = 10;
}
```

```php
// 대량 발송
use Illuminate\Support\Facades\Notification;

$users = User::whereNotNull('phone')->get();
Notification::send($users, new EventNotification());
```

---

## 9. v2 API 사용

`.env`에서 `SENDGO_API_VERSION=v2`만 설정하면 코드 변경 없이 v2로 동작합니다.

```env
SENDGO_API_VERSION=v2
```

v2 에러 코드 전체 목록 → [API_V2_ERROR_CODES.md](../API_V2_ERROR_CODES.md)

# SendGo Notification — Laravel 카카오톡·SMS 연동 패키지

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Version](https://img.shields.io/badge/version-1.1.1-blue.svg)](https://github.com/amuzcorp/sendgo-notification/releases)
[![Laravel](https://img.shields.io/badge/Laravel-8.x%20%7C%209.x%20%7C%2010.x%20%7C%2011.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-purple.svg)](https://php.net)

Laravel에서 **카카오 알림톡**, **친구톡**, **SMS / LMS / MMS**를 전송하는 공식 Notification 패키지입니다. [SendGo.io](https://www.sendgo.io) API v1 / v2를 모두 지원합니다.

---

## 목차

- [요구사항](#요구사항)
- [설치](#설치)
- [환경 설정](#환경-설정)
- [사용 방법](#사용-방법)
  - [알림톡 (AlimTalk)](#알림톡-alimtalk)
  - [친구톡 (FriendTalk)](#친구톡-friendtalk)
  - [SMS / LMS / MMS](#sms--lms--mms)
- [예외 처리](#예외-처리)
- [큐 비동기 발송](#큐-비동기-발송)
- [v1 → v2 마이그레이션](#v1--v2-마이그레이션)
- [에러 코드](#에러-코드)
- [트러블슈팅](#트러블슈팅)

---

## 요구사항

- **PHP** 8.2+
- **Laravel** 8.x | 9.x | 10.x | 11.x
- **SendGo 계정** ([sendgo.io](https://www.sendgo.io))

---

## 설치

```bash
composer require techigh/sendgo-notification
```

설정 파일 발행 (선택):

```bash
php artisan vendor:publish --tag=sendgo
```

---

## 환경 설정

`.env` 파일에 추가:

```env
SENDGO_URL=https://api.sendgo.io
SENDGO_ACCESS_KEY=your_access_key
SENDGO_SECRET_KEY=your_secret_key
SENDGO_SENDER_KEY=your_sms_sender_key
SENDGO_KAKAO_SENDER_KEY=your_kakao_sender_key

# API 버전 (기본값: v1 / v2 사용 시 명시)
SENDGO_API_VERSION=v1
```

> API 키는 [SendGo.io](https://www.sendgo.io) 콘솔 → **연동 정보**에서 확인할 수 있습니다.

---

## 사용 방법

### 알림톡 (AlimTalk)

사전 승인된 템플릿으로 공식 비즈니스 메시지를 전송합니다.

```php
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
            ->templateCode('ORDER_CONFIRM_001')
            ->replaceSms('Y')
            ->smsTitle('[주문 확인]')
            ->smsContent("주문 {$this->orderNumber}이 확인되었습니다.")
            ->to([
                'contact' => $notifiable->phone,
                'name'    => $notifiable->name,
                'var1'    => $this->orderNumber,
            ])
            ->at();
    }
}
```

```php
$user->notify(new OrderConfirmedNotification('ORD-20260101-001'));
```

#### AlimTalk 메서드

| 메서드 | 필수 | 설명 |
|--------|------|------|
| `templateCode(string)` | ✅ | SendGo에서 승인받은 템플릿 코드 |
| `scheduleType(string)` | | `'DIRECTLY'`(기본) 또는 `'SCHEDULED'` |
| `replaceSms(string)` | | 알림톡 실패 시 SMS 대체 (`'N'` 기본) |
| `smsTitle(string)` | 조건부 | 대체 SMS 제목 (replaceSms `'Y'`일 때 필수) |
| `smsContent(string)` | 조건부 | 대체 SMS 내용 (replaceSms `'Y'`일 때 필수) |
| `to(array)` | ✅ | 수신자 정보 (`contact` 필수, `name` / `var1`~`var8` 선택) |
| `at(string\|null)` | 조건부 | 예약 시각 (`SCHEDULED`일 때 필수, `Y-m-d H:i:s`) |

더 많은 예제 → [examples/AlimTalk.md](examples/AlimTalk.md)

---

### 친구톡 (FriendTalk)

카카오톡 채널 친구에게 자유로운 형태의 메시지를 전송합니다.

```php
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkChannel;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkMessage;

class NewProductNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return [FriendTalkChannel::class];
    }

    public function toFriend(object $notifiable): FriendTalkMessage
    {
        return FriendTalkMessage::make()
            ->messageType('FI')
            ->content("신상품 출시!\n\n{$this->product->name}\n{$this->product->price}원")
            ->imageUrl($this->product->image_url)
            ->imageLink($this->product->detail_url)
            ->buttons([
                [
                    'type'   => 'WL',
                    'name'   => '자세히 보기',
                    'linkMo' => $this->product->detail_url,
                    'linkPc' => $this->product->detail_url,
                ],
            ])
            ->to([
                'contact' => $notifiable->phone,
                'name'    => $notifiable->name,
            ])
            ->at();
    }
}
```

#### 메시지 타입

| 타입 | 설명 |
|------|------|
| `FT` | 텍스트형 |
| `FI` | 이미지형 |
| `FW` | 와이드 이미지형 (`wide('Y')` 필수) |
| `FL` | 와이드 아이템 리스트형 |
| `FM` | 커머스형 |
| `FC` | 캐러셀 피드형 |
| `FA` | 캐러셀 커머스형 |
| `FP` | 프리미엄 동영상형 |

#### FriendTalk 메서드

| 메서드 | 필수 | 설명 |
|--------|------|------|
| `messageType(string)` | ✅ | 메시지 타입 |
| `content(string)` | ✅ | 메시지 내용 |
| `scheduleType(string)` | | `'DIRECTLY'`(기본) 또는 `'SCHEDULED'` |
| `imageUrl(string)` | | 이미지 URL |
| `imageLink(string)` | | 이미지 클릭 링크 |
| `buttons(array)` | | 버튼 배열 |
| `wide(string)` | 조건부 | 와이드 이미지 여부 (FW일 때 `'Y'` 필수) |
| `adFlag(string)` | | 광고성 메시지 표기 (`'N'` 기본) |
| `replaceSms(string)` | | 친구톡 실패 시 SMS 대체 |
| `smsTitle(string)` | 조건부 | 대체 SMS 제목 |
| `smsContent(string)` | 조건부 | 대체 SMS 내용 |
| `to(array)` | ✅ | 수신자 정보 |
| `at(string\|null)` | 조건부 | 예약 시각 |

더 많은 예제 → [examples/FriendTalk.md](examples/FriendTalk.md)

---

### SMS / LMS / MMS

```php
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
            ->messageType('SMS')
            ->content("[인증번호] {$this->code} (3분 내 입력)")
            ->to([
                'contact' => $notifiable->phone,
                'name'    => $notifiable->name,
            ])
            ->at();
    }
}
```

**LMS** (장문, `subject` 필수):

```php
return SmsMessage::make()
    ->messageType('LMS')
    ->subject('[점검 안내]')
    ->content('2026-04-01 02:00~06:00 서비스 점검이 예정되어 있습니다.')
    ->to(['contact' => $notifiable->phone])
    ->at();
```

**MMS** (`subject` + `files` 필수, 최대 3개):

```php
return SmsMessage::make()
    ->messageType('MMS')
    ->subject('[이벤트]')
    ->content('첨부 이미지를 확인해주세요.')
    ->files([
        storage_path('app/public/event_01.jpg'),
        storage_path('app/public/event_02.jpg'),
    ])
    ->to(['contact' => $notifiable->phone])
    ->at();
```

#### SMS 메서드

| 메서드 | 필수 | 설명 |
|--------|------|------|
| `messageType(string)` | ✅ | `'SMS'` / `'LMS'` / `'MMS'` |
| `content(string)` | ✅ | 메시지 내용 |
| `campaignType(string)` | | `'MESSAGE'` (기본값) |
| `scheduleType(string)` | | `'DIRECTLY'`(기본) 또는 `'SCHEDULED'` |
| `subject(string)` | 조건부 | 제목 (LMS / MMS 필수) |
| `files(array)` | 조건부 | 파일 경로 배열 (MMS 필수, 최대 3개) |
| `to(array)` | ✅ | 수신자 정보 (`contact` 필수, `var1`~`var8` 선택) |
| `at(string\|null)` | 조건부 | 예약 시각 |

더 많은 예제 → [examples/SMS.md](examples/SMS.md)

---

## 예외 처리

모든 발송 실패는 `SendGoException`으로 던져집니다.

```php
use Techigh\SendgoNotification\Exceptions\SendGoException;

try {
    $user->notify(new OrderConfirmedNotification($order->number));
} catch (SendGoException $e) {
    $ctx = $e->context();
    // $ctx['error_code']  — 에러 코드 (예: 'INVALID_TEMPLATE_CODE')
    // $ctx['status']      — HTTP 상태 코드
    // $ctx['endpoint']    — 호출 엔드포인트
    // $ctx['api_version'] — 사용 중인 API 버전
    // $ctx['body']        — 원본 응답 body

    logger()->error('SendGo 발송 실패', [
        'error_code' => $ctx['error_code'] ?? null,
        'status'     => $ctx['status'] ?? null,
        'message'    => $e->getMessage(),
    ]);
}
```

---

## 큐 비동기 발송

`ShouldQueue`를 구현하면 Laravel 큐로 비동기 처리됩니다.

```php
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';
    public int $tries = 3;
    public int $backoff = 10;

    public function failed(SendGoException $e): void
    {
        logger()->critical('알림 최종 실패', ['error' => $e->getMessage()]);
    }
}
```

```bash
# 큐 워커 실행
php artisan queue:work --queue=notifications

# Redis 캐시 사용 권장 (다중 워커 환경)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

> 다중 queue worker 환경에서는 `redis` 또는 `database` 캐시 드라이버를 사용해야 토큰 캐시가 공유됩니다.

---

## v1 → v2 마이그레이션

Notification 코드 변경 없이 `.env` 한 줄만 바꾸면 됩니다.

```env
SENDGO_API_VERSION=v2
```

**v2 주요 차이점:**

| 항목 | v1 | v2 |
|------|----|----|
| 토큰 형식 | base64 인코딩 | 그대로 사용 (`sgv2.` prefix) |
| 토큰 재발급 | 401/403 무조건 재발급 | 에러 코드별 분기 (설정 오류는 재발급 안 함) |
| 에러 응답 | — | `code` / `message` / `traceId` 포함 |

v2 에러 코드 전체 목록 → [API_V2_ERROR_CODES.md](API_V2_ERROR_CODES.md)

---

## 에러 코드

`SendGoException::context()['error_code']`로 확인합니다.

### 인증 (401)

| 코드 | 설명 |
|------|------|
| `INVALID_AUTH_HEADER` | Authorization 헤더 없음 |
| `INVALID_BASIC_AUTH` | Basic 인증 형식 오류 |
| `INVALID_BASIC_AUTH_PAYLOAD` | Basic 인증 페이로드 오류 |
| `INVALID_ACCESS_KEY` | 유효하지 않은 Access Key |
| `INVALID_SECRET_KEY` | 유효하지 않은 Secret Key |
| `INVALID_BEARER_TOKEN` | Bearer 토큰 없음 |
| `INVALID_BEARER_TOKEN_PREFIX` | Bearer 토큰 prefix 오류 (v2) |
| `INVALID_BEARER_TOKEN_PAYLOAD` | Bearer 토큰 페이로드 오류 (v2) |
| `INVALID_BEARER_SIGNATURE` | Bearer 토큰 서명 불일치 (v2) |
| `INVALID_BEARER_APPLICATION` | 토큰에 해당하는 앱 없음 (v2) |
| `MALFORMED_BEARER_TOKEN` | 잘못된 형식의 Bearer 토큰 (v2) |
| `UNSUPPORTED_BEARER_TOKEN_VERSION` | 지원하지 않는 토큰 버전 (v2) |
| `TOKEN_MISMATCH` | 발급된 토큰과 불일치 (v2) |
| `TOKEN_EXPIRED` | 만료된 토큰 (자동 재발급 처리) |
| `TOKEN_RECORD_NOT_FOUND` | 토큰 레코드 없음 (v2) |

### 권한 (403)

| 코드 | 설명 |
|------|------|
| `ACCESS_KEY_NOT_APPROVED` | 미승인 Access Key |
| `IP_NOT_ALLOWED` | 허용되지 않은 IP |
| `TEAM_REQUIRED_FOR_KAKAO` | 카카오 API는 팀 소속 앱만 사용 가능 |
| `SENDER_APPLICATION_MISMATCH` | SMS 발신키가 앱과 불일치 |
| `KAKAO_SENDER_APPLICATION_MISMATCH` | 카카오 발신키가 앱과 불일치 |

### 리소스 (404)

| 코드 | 설명 |
|------|------|
| `INVALID_SENDER_KEY` | 유효하지 않은 SMS 발신키 |
| `INVALID_KAKAO_SENDER_KEY` | 유효하지 않은 카카오 발신키 |
| `INVALID_TEMPLATE_CODE` | 유효하지 않은 템플릿 코드 |
| `OWNER_NOT_FOUND` | 캠페인 소유자 확인 불가 |
| `SENDER_OWNER_NOT_FOUND` | SMS 발신키 소유자 확인 불가 |
| `KAKAO_SENDER_OWNER_NOT_FOUND` | 카카오 발신키 소유자 확인 불가 |

### 요청 (422)

| 코드 | 설명 |
|------|------|
| `EMPTY_CONTACTS` | 수신자 없음 |
| `PAYMENT_REQUIRED` | 크레딧 부족 |
| `MESSAGE_PROCESSING_FAILED` | SMS/MMS 처리 실패 |
| `NOTICE_PROCESSING_FAILED` | 알림톡 처리 실패 |
| `FRIEND_PROCESSING_FAILED` | 친구톡 처리 실패 |

### 패키지 내부

| 코드 | 설명 |
|------|------|
| `INVALID_API_VERSION` | 지원하지 않는 API 버전 |

v2 에러 코드 상세 → [API_V2_ERROR_CODES.md](API_V2_ERROR_CODES.md)

---

## 트러블슈팅

**메시지가 전송되지 않을 때**
1. `.env` API 키 확인
2. SendGo 계정 잔액 확인
3. 전화번호 형식 확인 (`01012345678`, 하이픈 제외)
4. `storage/logs/laravel.log` 에러 확인

**알림톡이 SMS로 대체 발송될 때**
1. SendGo에서 템플릿 승인 상태 확인
2. 템플릿 변수 매핑 확인
3. 카카오 채널 연동 상태 확인

**다중 워커에서 토큰 충돌이 발생할 때**
- `CACHE_DRIVER=redis`로 변경 (공유 캐시 필수)

**`INVALID_API_VERSION` 에러**
- `SENDGO_API_VERSION` 값이 `v1` 또는 `v2`인지 확인

---

## 라이선스

MIT — [LICENSE](LICENSE) 참조

---

**Made with ❤️ by [Techigh](https://github.com/techigh) | Powered by [SendGo.io](https://www.sendgo.io)**

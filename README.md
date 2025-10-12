# 라라벨 카카오톡 알림톡·친구톡·SMS 연동 패키지 | Laravel Kakao Notification

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Version](https://img.shields.io/badge/version-1.0.2-blue.svg)](https://github.com/techigh/sendgo-notification)
[![Laravel](https://img.shields.io/badge/Laravel-8.x%20%7C%209.x%20%7C%2010.x%20%7C%2011.x-red.svg)](https://laravel.com)

**Laravel 알림톡, 친구톡, SMS, LMS, MMS 쉬운 연동 패키지** - SendGo.io 기반

## 📌 목차

- [소개](#소개)
- [주요 기능](#주요-기능)
- [요구사항](#요구사항)
- [설치 방법](#설치-방법)
- [환경 설정](#환경-설정)
- [사용 방법](#사용-방법)
  - [카카오 알림톡 전송](#1-카카오-알림톡-alimtalk-전송)
  - [카카오 친구톡 전송](#2-카카오-친구톡-friendtalk-전송)
  - [SMS 전송](#3-sms-전송)
  - [LMS 전송](#4-lms-전송)
  - [MMS 전송](#5-mms-전송)
- [고급 기능](#고급-기능)
- [SendGo란?](#sendgo란)
- [문의 및 지원](#문의-및-지원)
- [라이선스](#라이선스)

---

## 🎯 소개

**techigh/sendgo-notification**은 Laravel 프레임워크에서 **카카오톡 알림톡(AlimTalk)**, **친구톡(FriendTalk)**, **SMS**, **LMS**, **MMS** 메시지를 쉽게 전송할 수 있도록 도와주는 공식 Notification 패키지입니다.

Laravel의 강력한 Notification 시스템과 [SendGo.io](https://www.sendgo.io)의 안정적인 메시징 API를 결합하여, 개발자가 몇 줄의 코드만으로 다양한 메시징 채널을 통합할 수 있습니다.

### 🚀 왜 이 패키지를 선택해야 하나요?

- ✅ **Laravel Notification 네이티브 지원**: Laravel의 표준 알림 시스템과 완벽 호환
- ✅ **다양한 메시징 채널**: 알림톡, 친구톡, SMS, LMS, MMS를 하나의 패키지로
- ✅ **간편한 설정**: 환경 변수만 설정하면 즉시 사용 가능
- ✅ **예약 발송 지원**: 즉시 발송 또는 예약 발송 모두 가능
- ✅ **대체 발송 기능**: 카카오 메시지 실패 시 자동으로 SMS로 대체 발송
- ✅ **템플릿 변수 지원**: 동적 데이터를 쉽게 주입 가능
- ✅ **SendGo.io 공식 지원**: 안정적이고 빠른 메시지 전송

---

## ✨ 주요 기능

### 1. 카카오 알림톡 (AlimTalk)
- 사전 승인된 템플릿으로 공식 비즈니스 메시지 발송
- 템플릿 변수 지원 (동적 데이터 주입)
- 대체 SMS 자동 발송 지원
- 즉시 발송 및 예약 발송

### 2. 카카오 친구톡 (FriendTalk)
- 카카오톡 채널 친구에게 자유로운 형태의 메시지 발송
- 다양한 메시지 타입 지원:
  - **FT**: 텍스트형
  - **FI**: 이미지형
  - **FW**: 와이드 이미지형
  - **FL**: 와이드 아이템 리스트형
  - **FM**: 커머스형
  - **FC**: 캐러셀 피드형
  - **FA**: 캐러셀 커머스형
  - **FP**: 프리미엄 동영상형
- 이미지, 버튼, 링크 첨부 가능
- 광고성 메시지 표기 지원

### 3. SMS / LMS / MMS
- **SMS**: 단문 메시지 (최대 90바이트)
- **LMS**: 장문 메시지 (최대 2,000바이트)
- **MMS**: 멀티미디어 메시지 (이미지 첨부, 최대 3개 파일)
- 예약 발송 지원
- 대량 발송 가능

---

## 📋 요구사항

- **PHP**: 8.2 이상
- **Laravel**: 8.x | 9.x | 10.x | 11.x
- **Composer**: 최신 버전
- **SendGo 계정**: [SendGo.io](https://www.sendgo.io)에서 무료 가입

---

## 📦 설치 방법

### 1. Composer로 패키지 설치

터미널에서 다음 명령어를 실행하세요:

```bash
composer require techigh/sendgo-notification
```

Laravel의 패키지 자동 탐색 기능으로 서비스 프로바이더가 자동으로 등록됩니다.

### 2. 설정 파일 발행 (선택사항)

필요한 경우 설정 파일을 프로젝트로 복사할 수 있습니다:

```bash
php artisan vendor:publish --tag=sendgo
```

이 명령어는 `config/sendgo.php` 파일을 생성합니다.

```bash
composer dump-autoload
```

---

## ⚙️ 환경 설정

### 1. SendGo API 키 발급

[SendGo.io](https://www.sendgo.io)에 로그인하여 다음 정보를 발급받으세요:

1. **[연동 정보]** 메뉴로 이동
2. **API Keys** 섹션에서 다음 정보 확인:
   - `SENDGO_ACCESS_KEY`
   - `SENDGO_SECRET_KEY`
3. **발신 Keys** 섹션에서 다음 정보 확인:
   - `SENDGO_SENDER_KEY` (SMS/LMS/MMS 발신번호)
   - `SENDGO_KAKAO_SENDER_KEY` (카카오 채널 발신키)

### 2. 환경 변수 설정

프로젝트의 `.env` 파일에 다음 내용을 추가하세요:

```env
# SendGo API 설정
SENDGO_ACCESS_KEY=your_access_key_here
SENDGO_SECRET_KEY=your_secret_key_here
SENDGO_SENDER_KEY=your_sms_sender_key_here
SENDGO_KAKAO_SENDER_KEY=your_kakao_sender_key_here
```

> ⚠️ **중요**: API 키는 절대 Git에 커밋하지 마세요! `.env` 파일은 `.gitignore`에 포함되어 있어야 합니다.

### 3. 설정 확인

다음 명령어로 설정이 올바른지 확인할 수 있습니다:

```bash
php artisan config:clear
php artisan config:cache
```

---

## 📖 사용 방법

### 기본 사용 흐름

1. **Notification 클래스 생성**
2. **메시지 타입별 메서드 구현** (`toAlim`, `toFriend`, `toSms`)
3. **사용자에게 알림 전송**

---

### 1. 카카오 알림톡 (AlimTalk) 전송

알림톡은 사전에 승인된 템플릿을 사용하여 공식 비즈니스 메시지를 전송합니다.

#### Step 1: Notification 클래스 생성

```bash
php artisan make:notification OrderShippedNotification
```

#### Step 2: Notification 클래스 구현

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkChannel;
use Techigh\SendgoNotification\Attributes\Alim\AlimTalkMessage;

class OrderShippedNotification extends Notification
{
    use Queueable;

    private $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * 알림 채널 지정
     */
    public function via($notifiable): array
    {
        return [AlimTalkChannel::class];
    }

    /**
     * 알림톡 메시지 구성
     */
    public function toAlim($notifiable): AlimTalkMessage
    {
        return AlimTalkMessage::make()
            ->templateCode('ORDER_001') // SendGo에서 승인받은 템플릿 코드
            ->scheduleType('DIRECTLY') // 즉시 발송
            ->replaceSms('Y') // 알림톡 실패 시 SMS로 대체 발송
            ->smsTitle('[주문 배송 안내]') // 대체 SMS 제목
            ->smsContent("주문하신 상품이 배송 시작되었습니다.\n송장번호: {$this->order->tracking_number}")
            ->to([
                'contact' => $notifiable->phone, // 수신자 전화번호 (필수)
                'name' => $notifiable->name, // 수신자 이름 (선택)
                'var1' => $this->order->order_number, // 템플릿 변수
                'var2' => $this->order->tracking_number, // 템플릿 변수
                'var3' => $this->order->product_name, // 템플릿 변수
            ])
            ->at(); // 즉시 발송 (비워두면 즉시 발송)
    }
}
```

#### Step 3: 사용자에게 알림 전송

```php
use App\Models\User;
use App\Notifications\OrderShippedNotification;

// 단일 사용자에게 전송
$user = User::find(1);
$user->notify(new OrderShippedNotification($order));

// 여러 사용자에게 전송
$users = User::where('order_status', 'shipped')->get();
foreach ($users as $user) {
    $user->notify(new OrderShippedNotification($order));
}
```

#### 예약 발송 예제

```php
public function toAlim($notifiable): AlimTalkMessage
{
    return AlimTalkMessage::make()
        ->templateCode('PROMO_001')
        ->scheduleType('SCHEDULED') // 예약 발송
        ->to([
            'contact' => $notifiable->phone,
            'name' => $notifiable->name,
            'var1' => '특별 할인 이벤트',
        ])
        ->at('2025-10-15 09:00:00'); // 예약 시각 (Y-m-d H:i:s 형식)
}
```

#### 알림톡 메서드 상세 설명

| 메서드 | 필수 여부 | 설명 | 예시 |
|--------|----------|------|------|
| `templateCode()` | ✅ 필수 | SendGo에서 승인받은 템플릿 코드 | `'ORDER_001'` |
| `scheduleType()` | 선택 | 발송 방식 (`DIRECTLY` 또는 `SCHEDULED`) | `'DIRECTLY'` (기본값) |
| `replaceSms()` | 선택 | 알림톡 실패 시 SMS 대체 발송 여부 (`Y` 또는 `N`) | `'N'` (기본값) |
| `smsTitle()` | 조건부 | 대체 SMS 제목 (replaceSms가 `Y`일 때 필수) | `'주문 안내'` |
| `smsContent()` | 조건부 | 대체 SMS 내용 (replaceSms가 `Y`일 때 필수) | `'주문이 완료되었습니다.'` |
| `to()` | ✅ 필수 | 수신자 정보 및 템플릿 변수 | `['contact' => '01012345678', ...]` |
| `at()` | 조건부 | 예약 발송 시각 (scheduleType이 `SCHEDULED`일 때 필수) | `'2025-10-15 09:00:00'` |

---

### 2. 카카오 친구톡 (FriendTalk) 전송

친구톡은 카카오톡 채널 친구에게 자유로운 형태의 메시지를 전송합니다.

#### Step 1: Notification 클래스 생성

```bash
php artisan make:notification NewProductNotification
```

#### Step 2: Notification 클래스 구현

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkChannel;
use Techigh\SendgoNotification\Attributes\Friend\FriendTalkMessage;

class NewProductNotification extends Notification
{
    use Queueable;

    private $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function via($notifiable): array
    {
        return [FriendTalkChannel::class];
    }

    public function toFriend($notifiable): FriendTalkMessage
    {
        return FriendTalkMessage::make()
            ->scheduleType('DIRECTLY') // 즉시 발송
            ->messageType('FI') // 이미지형 메시지
            ->content("🎉 신상품 출시!\n\n{$this->product->name}\n가격: {$this->product->price}원")
            ->imageUrl($this->product->image_url) // 이미지 URL
            ->imageLink($this->product->detail_url) // 이미지 클릭 시 이동할 링크
            ->buttons([ // 버튼 추가
                [
                    'type' => 'WL', // 웹 링크
                    'name' => '자세히 보기',
                    'linkMo' => $this->product->detail_url, // 모바일 링크
                    'linkPc' => $this->product->detail_url, // PC 링크
                ],
                [
                    'type' => 'WL',
                    'name' => '구매하기',
                    'linkMo' => $this->product->purchase_url,
                    'linkPc' => $this->product->purchase_url,
                ],
            ])
            ->wide('N') // 와이드 이미지 여부
            ->adFlag('N') // 광고성 메시지 표기 여부
            ->replaceSms('Y') // 친구톡 실패 시 SMS 대체 발송
            ->smsTitle('[신상품 안내]')
            ->smsContent("신상품이 출시되었습니다!\n{$this->product->name}")
            ->to([
                'contact' => $notifiable->phone,
                'name' => $notifiable->name,
            ])
            ->at();
    }
}
```

#### Step 3: 사용자에게 알림 전송

```php
use App\Models\User;
use App\Notifications\NewProductNotification;

$user = User::find(1);
$user->notify(new NewProductNotification($product));
```

#### 친구톡 메시지 타입

| 타입 | 설명 | 사용 예시 |
|------|------|----------|
| `FT` | 텍스트형 | 간단한 텍스트 메시지 |
| `FI` | 이미지형 | 이미지와 텍스트 조합 |
| `FW` | 와이드 이미지형 | 큰 이미지 배너 |
| `FL` | 와이드 아이템 리스트형 | 상품 목록 형태 |
| `FM` | 커머스형 | 쇼핑몰 상품 소개 |
| `FC` | 캐러셀 피드형 | 여러 이미지 슬라이드 |
| `FA` | 캐러셀 커머스형 | 여러 상품 슬라이드 |
| `FP` | 프리미엄 동영상형 | 동영상 메시지 |

#### 친구톡 메서드 상세 설명

| 메서드 | 필수 여부 | 설명 | 예시 |
|--------|----------|------|------|
| `messageType()` | ✅ 필수 | 메시지 타입 (FT, FI, FW 등) | `'FI'` |
| `content()` | ✅ 필수 | 메시지 내용 | `'신상품이 출시되었습니다!'` |
| `image()` | 조건부 | 이미지 파일 (File 객체, FI/FW/FL일 때 필수) | `$file` |
| `imageUrl()` | 선택 | 이미지 URL | `'https://example.com/image.jpg'` |
| `imageLink()` | 선택 | 이미지 클릭 시 이동할 링크 | `'https://example.com/product'` |
| `buttons()` | 선택 | 버튼 배열 | `[['type' => 'WL', 'name' => '보기', ...]]` |
| `wide()` | 조건부 | 와이드 이미지 여부 (FW일 때 `Y` 필수) | `'N'` (기본값) |
| `adFlag()` | 선택 | 광고성 메시지 표기 여부 | `'N'` (기본값) |
| `replaceSms()` | 선택 | 친구톡 실패 시 SMS 대체 발송 여부 | `'N'` (기본값) |

---

### 3. SMS 전송

단문 메시지 서비스로 최대 90바이트(한글 45자)까지 전송 가능합니다.

#### Notification 클래스 구현

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Techigh\SendgoNotification\Attributes\Sms\SmsChannel;
use Techigh\SendgoNotification\Attributes\Sms\SmsMessage;

class VerificationCodeNotification extends Notification
{
    use Queueable;

    private $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms($notifiable): SmsMessage
    {
        return SmsMessage::make()
            ->campaignType('MESSAGE') // 캠페인 타입
            ->messageType('SMS') // SMS 타입
            ->scheduleType('DIRECTLY') // 즉시 발송
            ->content("[인증번호] {$this->code}\n3분 내에 입력해주세요.")
            ->to([
                'contact' => $notifiable->phone, // 필수
                'name' => $notifiable->name, // 선택
            ])
            ->at();
    }
}
```

#### 사용 예시

```php
use App\Models\User;
use App\Notifications\VerificationCodeNotification;

$user = User::find(1);
$verificationCode = '123456';
$user->notify(new VerificationCodeNotification($verificationCode));
```

---

### 4. LMS 전송

장문 메시지 서비스로 최대 2,000바이트(한글 1,000자)까지 전송 가능합니다.

#### Notification 클래스 구현

```php
public function toSms($notifiable): SmsMessage
{
    return SmsMessage::make()
        ->campaignType('MESSAGE')
        ->messageType('LMS') // LMS 타입
        ->scheduleType('DIRECTLY')
        ->subject('[중요 공지사항]') // LMS는 제목 필수
        ->content(
            "안녕하세요, {$notifiable->name}님.\n\n" .
            "서비스 점검 안내드립니다.\n\n" .
            "점검 일시: 2025년 10월 15일 02:00 ~ 06:00\n" .
            "점검 내용: 시스템 업그레이드\n\n" .
            "점검 시간 동안 서비스 이용이 불가하오니 양해 부탁드립니다.\n\n" .
            "감사합니다."
        )
        ->to([
            'contact' => $notifiable->phone,
            'name' => $notifiable->name,
        ])
        ->at();
}
```

---

### 5. MMS 전송

멀티미디어 메시지 서비스로 이미지를 첨부할 수 있습니다 (최대 3개 파일).

#### Notification 클래스 구현

```php
public function toSms($notifiable): SmsMessage
{
    return SmsMessage::make()
        ->campaignType('MESSAGE')
        ->messageType('MMS') // MMS 타입
        ->scheduleType('DIRECTLY')
        ->subject('[이벤트 안내]') // MMS는 제목 필수
        ->content(
            "🎉 특별 이벤트 진행 중!\n\n" .
            "자세한 내용은 첨부 이미지를 확인해주세요."
        )
        ->files([ // MMS는 파일 첨부 필수 (최대 3개)
            storage_path('app/public/event_image1.jpg'),
            storage_path('app/public/event_image2.jpg'),
        ])
        ->to([
            'contact' => $notifiable->phone,
            'name' => $notifiable->name,
        ])
        ->at();
}
```

#### SMS/LMS/MMS 메서드 상세 설명

| 메서드 | 필수 여부 | 설명 | 예시 |
|--------|----------|------|------|
| `campaignType()` | 선택 | 캠페인 타입 | `'MESSAGE'` (기본값) |
| `messageType()` | ✅ 필수 | 메시지 타입 (`SMS`, `LMS`, `MMS`) | `'SMS'` |
| `scheduleType()` | 선택 | 발송 방식 (`DIRECTLY` 또는 `SCHEDULED`) | `'DIRECTLY'` (기본값) |
| `content()` | ✅ 필수 | 메시지 내용 | `'인증번호: 123456'` |
| `subject()` | 조건부 | 제목 (LMS/MMS일 때 필수) | `'[중요 공지]'` |
| `files()` | 조건부 | 첨부 파일 배열 (MMS일 때 필수, 최대 3개) | `['path/to/image.jpg']` |
| `to()` | ✅ 필수 | 수신자 정보 | `['contact' => '01012345678', ...]` |
| `at()` | 조건부 | 예약 발송 시각 (SCHEDULED일 때 필수) | `'2025-10-15 09:00:00'` |

---

## 🎓 고급 기능

### 1. 템플릿 변수 활용 (알림톡)

알림톡 템플릿에서 변수를 사용하여 동적 데이터를 주입할 수 있습니다.

```php
->to([
    'contact' => $notifiable->phone,
    'name' => $notifiable->name,
    'var1' => $order->order_number,      // 주문번호
    'var2' => $order->product_name,      // 상품명
    'var3' => $order->price,             // 가격
    'var4' => $order->delivery_date,     // 배송일
    'var5' => $order->tracking_number,   // 송장번호
    // ... 최대 var8까지 지원
])
```

### 2. 큐(Queue) 활용

대량 발송 시 큐를 사용하여 비동기 처리할 수 있습니다.

```php
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderShippedNotification;

// 여러 사용자에게 비동기로 전송
$users = User::where('status', 'active')->get();
Notification::send($users, new OrderShippedNotification($order));
```

Laravel 큐 설정:

```bash
# .env 파일에 큐 드라이버 설정
QUEUE_CONNECTION=redis

# 큐 워커 실행
php artisan queue:work
```

### 3. 조건부 채널 선택

사용자 설정에 따라 알림 채널을 동적으로 선택할 수 있습니다.

```php
public function via($notifiable): array
{
    $channels = [];
    
    if ($notifiable->notification_preferences['alimtalk']) {
        $channels[] = AlimTalkChannel::class;
    }
    
    if ($notifiable->notification_preferences['sms']) {
        $channels[] = SmsChannel::class;
    }
    
    return $channels;
}
```

### 4. 발송 결과 로깅

메시지 발송 결과를 로깅하여 추적할 수 있습니다.

```php
use Illuminate\Support\Facades\Log;

public function toAlim($notifiable): AlimTalkMessage
{
    Log::info('AlimTalk sending', [
        'user_id' => $notifiable->id,
        'phone' => $notifiable->phone,
        'template' => 'ORDER_001',
    ]);
    
    return AlimTalkMessage::make()
        ->templateCode('ORDER_001')
        // ...
}
```

---

## 🌟 SendGo란?

**[SendGo.io](https://www.sendgo.io)**는 대한민국의 선도적인 메시징 플랫폼으로, 기업이 고객과 효율적으로 소통할 수 있도록 다양한 메시징 서비스를 제공합니다.

### SendGo 주요 특징

- ✅ **카카오톡 공식 파트너**: 알림톡, 친구톡 공식 제공
- ✅ **높은 도달률**: 99.9% 이상의 메시지 전송 성공률
- ✅ **빠른 전송 속도**: 초당 수천 건의 메시지 처리
- ✅ **안정적인 인프라**: 24/7 무중단 서비스
- ✅ **합리적인 가격**: 건당 과금 방식으로 부담 없는 요금제
- ✅ **실시간 통계**: 발송 현황 및 결과를 실시간으로 확인
- ✅ **API 연동 간편**: RESTful API로 쉬운 통합
- ✅ **전문 기술 지원**: 빠르고 친절한 고객 지원

### SendGo 무료 체험

SendGo는 회원 가입 시 **무료 크레딧**을 제공하여 서비스를 체험해볼 수 있습니다.

👉 [SendGo.io에서 무료로 시작하기](https://www.sendgo.io)

---

## 💡 사용 예시 시나리오

### 1. 이커머스 쇼핑몰
- 주문 확인 알림톡
- 배송 시작 알림톡
- 배송 완료 친구톡
- 리뷰 작성 요청 SMS

### 2. 회원 서비스
- 회원 가입 인증번호 SMS
- 비밀번호 재설정 인증번호 SMS
- 이벤트 알림 친구톡
- 정기 소식지 LMS

### 3. 예약 시스템
- 예약 확인 알림톡
- 예약 리마인더 SMS
- 예약 변경 알림톡
- 예약 취소 안내 SMS

### 4. 교육 플랫폼
- 수업 시작 알림톡
- 과제 제출 리마인더 SMS
- 성적 공지 LMS
- 이벤트 홍보 친구톡

---

## 🔧 트러블슈팅

### 문제: 메시지가 전송되지 않아요

**해결 방법:**
1. `.env` 파일의 API 키 확인
2. SendGo 계정의 잔액 확인
3. 전화번호 형식 확인 (예: `01012345678`, 하이픈 제외)
4. 템플릿 코드 확인 (알림톡의 경우)
5. 로그 파일 확인 (`storage/logs/laravel.log`)

### 문제: 알림톡이 SMS로 대체 발송돼요

**원인:**
- 템플릿 코드가 승인되지 않음
- 템플릿 변수가 일치하지 않음
- 카카오 채널이 활성화되지 않음

**해결 방법:**
1. SendGo에서 템플릿 승인 상태 확인
2. 템플릿 변수 매핑 확인
3. 카카오 채널 연동 상태 확인

### 문제: 이미지가 전송되지 않아요 (친구톡/MMS)

**해결 방법:**
1. 이미지 파일 크기 확인 (최대 500KB 권장)
2. 이미지 형식 확인 (JPG, PNG 지원)
3. 파일 경로 확인 (절대 경로 사용)
4. 파일 읽기 권한 확인

---

## 📚 추가 자료

- [SendGo 공식 문서](https://www.sendgo.io/docs)
- [Laravel Notification 공식 문서](https://laravel.com/docs/notifications)
- [카카오 알림톡 가이드](https://www.sendgo.io/docs/alimtalk)
- [카카오 친구톡 가이드](https://www.sendgo.io/docs/friendtalk)

---

## 📞 문의 및 지원

- **이메일**: techigh@amuz.co.kr
- **GitHub Issues**: [https://github.com/techigh/sendgo-notification/issues](https://github.com/techigh/sendgo-notification/issues)
- **SendGo 고객센터**: [https://www.sendgo.io/support](https://www.sendgo.io/support)

문제가 발생하거나 기능 요청이 있으시면 언제든지 연락주세요!

---

## 📄 라이선스

이 패키지는 MIT 라이선스 하에 배포됩니다. 자세한 내용은 [LICENSE](LICENSE) 파일을 참조하세요.

---

## 🙏 기여하기

Pull Request와 Issue는 언제나 환영합니다!

1. 이 저장소를 포크하세요
2. 새로운 브랜치를 생성하세요 (`git checkout -b feature/amazing-feature`)
3. 변경사항을 커밋하세요 (`git commit -m 'Add some amazing feature'`)
4. 브랜치에 푸시하세요 (`git push origin feature/amazing-feature`)
5. Pull Request를 생성하세요

---

## 🏷️ 키워드

`라라벨`, `Laravel`, `카카오톡`, `알림톡`, `AlimTalk`, `친구톡`, `FriendTalk`, `SMS`, `LMS`, `MMS`, `문자 메시지`, `Notification`, `메시징`, `SendGo`, `PHP`, `Laravel 패키지`, `카카오 비즈니스`, `메시지 발송`, `알림 시스템`, `Laravel Notification Channel`, `한국 메시징`, `비즈니스 메시지`

---

## ⭐ Star History

이 프로젝트가 도움이 되셨다면 ⭐️ Star를 눌러주세요!

---

**Made with ❤️ by [Techigh](https://github.com/techigh) | Powered by [SendGo.io](https://www.sendgo.io)**

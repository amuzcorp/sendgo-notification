# SendGo Notification Package for Laravel

## Installation

- `required` 를 통해 패키지를 설치합니다.

```shell
composer require techigh/sendgo-notification
```

### `.env`

- `.env` 에 아래의 환경변수가 추가되어야 합니다. 아래의 환경변수들은 샌드고(www.sendgo.io)-[연동하기]-[연동 정보]에서 승인된 연동에 관해 발급 받을 수 있습니다.
- `SENDGO_ACCESS_KEY`와 `SENDGO_SECRECT_KEY`는 [연동 정보] 탭의 API Keys 에서 확인할 수 있습니다.
- `SENDGO_SENDER_KEY`와 `SENDGO_KAKAO_SENDER_KEY`는 [연동 정보] 탭의 발신 Keys 에서 해당 발신번호와 카카오 채널에 탭에서 확인할 수 있습니다.

```bash
SENDGO_ACCESS_KEY=your_access_key
SENDGO_SECRET_KEY=your_secret_key
SENDGO_SENDER_KEY=your_sms_sender_key
SENDGO_KAKAO_SENDER_KEY=your_kakao_sender_key
```

### Config

필요한 경우, 설정 파일 및 마이그레이션 파일을 프로젝트 루트로 게시하여 활용할 수 있습니다. 다음 태그를 이용해 적절히 게시하면 됩니다.

```shell
php artisan vendor:publish --tag=sendgo
```

```shell
composer dump-autoload
```

---

## Usage

### [SMS](examples/SMS.md)

### [AlimTalk](examples/AlimTalk.md)

### [FriendTalk](examples/FriendTalk.md)

# SendGo Notification Package for Laravel

## Installation

You can install the package using composer

```shell
composer require techigh/sendgo-notification
```

### `.env`

```bash
SENDGO_ACCESS_KEY=your_access_key
SENDGO_SECRET_KEY=your_secret_key
SENDGO_SENDER_KEY=your_sms_sender_key
SENDGO_KAKAO_SENDER_KEY=your_kakao_sender_key
```

### Config

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

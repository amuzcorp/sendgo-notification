# SendGo Notification Package for Laravel

## Installation

You can install the package using composer

```shell
$ composer require techigh/sendgo-notification
```

### `.env`

```bash
SENDGO_ACCESS_KEY=
SENDGO_SECRET_KEY=
SENDGO_SENDER_KEY=
SENDGO_KAKAO_SENDER_KEY=
```

### Config

```shell
$ php artisan vendor:publish --tag=sendgo
```

```shell
$ composer dump-autoload
```

---

## Usage

### [SMS](examples/SMS.md)

### [AlimTalk](examples/AlimTalk.md)

### [FriendTalk](examples/FriendTalk.md)

# SendGo Notification Package for Laravel

## Installation

You can install the package using composer

```shell
$ composer require techigh/sendgo-notification
```

### `.env`

```bash
SENDGO_DEBUG=
SENDGO_ACCESS_KEY=
SENDGO_SECRET_KEY=
SENDGO_SENDER_KEY=
SENDGO_KAKAO_SENDER_KEY=
```

- for Debug `.env`

```bash
SENDGO_DEBUG=true
SENDGO_ACCESS_KEY=your_access_key
SENDGO_SECRET_KEY=your_secret_key
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

### Alim

### Friend

# SendGo Notification API v2 Error Codes

`sendgo-notification` 패키지가 `smpp-provider`의 `/api/v2/*` 라인과 통신할 때 기대하는 예외 코드 문서다. 서버 기준 원본은 `smpp-provider/docs/application/features/api-v2-error-codes.md`이며, 이 문서는 패키지 소비자 관점의 해석 기준을 함께 정리한다.

## Response Shape

`v2` 에러 응답은 다음 형태를 기본으로 사용한다.

```json
{
  "traceId": "optional-trace-id",
  "code": "TOKEN_EXPIRED",
  "message": "Expired Token",
  "errors": [],
  "timestamp": "2026-03-30 12:00:00"
}
```

- `code`: 패키지 분기 기준
- `message`: 사람 읽기용 메시지
- `traceId`: 서버 로그 추적용
- `errors`: 세부 검증 오류

## Authentication

### Basic Authorization

| Code | HTTP | Meaning | Package Action |
| --- | ---: | --- | --- |
| `INVALID_AUTH_HEADER` | 401 | `Authorization` 헤더가 없거나 비어 있다. | 재시도하지 않고 실패 |
| `INVALID_BASIC_AUTH` | 401 | `Basic` 스킴이 아니다. | 재시도하지 않고 실패 |
| `INVALID_BASIC_AUTH_PAYLOAD` | 401 | base64 decode 실패 또는 `accessKey:secretKey` 형식이 아니다. | 재시도하지 않고 실패 |
| `INVALID_ACCESS_KEY` | 401 | `access_key`에 해당하는 application이 없다. | 재시도하지 않고 실패 |
| `INVALID_SECRET_KEY` | 401 | `secret_key`가 일치하지 않는다. | 재시도하지 않고 실패 |
| `ACCESS_KEY_NOT_APPROVED` | 403 | application 상태가 `SUCCESS`가 아니다. | 재시도하지 않고 실패 |

### Bearer Token

| Code | HTTP | Meaning | Package Action |
| --- | ---: | --- | --- |
| `INVALID_BEARER_TOKEN` | 401 | Bearer 토큰이 비어 있다. | 토큰 재발급 후 1회 재시도 |
| `INVALID_BEARER_TOKEN_PREFIX` | 401 | `sgv2.` prefix가 아니다. | 토큰 재발급 후 1회 재시도 |
| `MALFORMED_BEARER_TOKEN` | 401 | 토큰 분해 형식이 잘못되었다. | 토큰 재발급 후 1회 재시도 |
| `INVALID_BEARER_SIGNATURE` | 401 | HMAC 서명이 일치하지 않는다. | 토큰 재발급 후 1회 재시도 |
| `INVALID_BEARER_TOKEN_PAYLOAD` | 401 | payload decode 또는 필수 payload 값 파싱에 실패했다. | 토큰 재발급 후 1회 재시도 |
| `UNSUPPORTED_BEARER_TOKEN_VERSION` | 401 | `version !== v2` 이다. | 토큰 재발급 후 1회 재시도 |
| `INVALID_BEARER_APPLICATION` | 401 | payload의 `application_id`에 해당하는 application이 없다. | 재시도하지 않고 실패 |
| `TOKEN_RECORD_NOT_FOUND` | 401 | application에 연결된 token 레코드가 없다. | 토큰 재발급 후 1회 재시도 |
| `TOKEN_MISMATCH` | 401 | 현재 저장된 `token_v2`와 요청 토큰이 다르다. | 토큰 재발급 후 1회 재시도 |
| `TOKEN_EXPIRED` | 401 | `token_v2_expires_at`이 지났다. | 토큰 재발급 후 1회 재시도 |

## Network Policy

| Code | HTTP | Meaning | Package Action |
| --- | ---: | --- | --- |
| `IP_NOT_ALLOWED` | 403 | application whitelist IP 정책에 맞지 않는다. | 재시도하지 않고 실패 |

## Sender Ownership

### SMS Sender

| Code | HTTP | Meaning | Package Action |
| --- | ---: | --- | --- |
| `INVALID_SENDER_KEY` | 404 | `senderKey`가 존재하지 않는다. | 재시도하지 않고 실패 |
| `SENDER_OWNER_NOT_FOUND` | 404 | `senderKey`는 있으나 owner를 찾을 수 없다. | 재시도하지 않고 실패 |
| `SENDER_APPLICATION_MISMATCH` | 403 | application owner와 sender owner가 다르다. | 재시도하지 않고 실패 |

### Kakao Sender

| Code | HTTP | Meaning | Package Action |
| --- | ---: | --- | --- |
| `INVALID_KAKAO_SENDER_KEY` | 404 | `kakaoSenderKey`가 존재하지 않는다. | 재시도하지 않고 실패 |
| `KAKAO_SENDER_OWNER_NOT_FOUND` | 404 | `kakaoSenderKey`는 있으나 owner 팀을 찾을 수 없다. | 재시도하지 않고 실패 |
| `TEAM_REQUIRED_FOR_KAKAO` | 403 | 카카오 API는 team-owned application만 사용할 수 있다. | 재시도하지 않고 실패 |
| `KAKAO_SENDER_APPLICATION_MISMATCH` | 403 | application team과 kakao sender team이 다르다. | 재시도하지 않고 실패 |

## Campaign / Domain Errors

| Code | HTTP | Meaning | Package Action |
| --- | ---: | --- | --- |
| `INVALID_TEMPLATE_CODE` | 404 | 알림톡 템플릿 코드를 찾지 못했다. | 재시도하지 않고 실패 |
| `OWNER_NOT_FOUND` | 404 | global message owner를 식별하지 못했다. | 재시도하지 않고 실패 |
| `EMPTY_CONTACTS` | 422 | 전송 대상이 비어 있다. | payload 수정 필요 |
| `PAYMENT_REQUIRED` | 402 | 크레딧 부족 또는 결제가 필요하다. | 재시도하지 않고 실패 |
| `MESSAGE_PROCESSING_FAILED` | 422 | SMS/MMS 전송 도메인 처리 중 실패했다. | payload 또는 서버 상태 확인 |
| `NOTICE_PROCESSING_FAILED` | 422 | 알림톡 전송 도메인 처리 중 실패했다. | payload 또는 서버 상태 확인 |
| `FRIEND_PROCESSING_FAILED` | 422 | 친구톡 전송 도메인 처리 중 실패했다. | payload 또는 서버 상태 확인 |
| `GLOBAL_MESSAGE_PROCESSING_FAILED` | 422 | 글로벌 메시지 전송 도메인 처리 중 실패했다. | payload 또는 서버 상태 확인 |

## Generic Fallback

| Code | HTTP | Meaning | Package Action |
| --- | ---: | --- | --- |
| `NOT_FOUND` | 404 | 조회/연관 모델을 찾지 못했다. | 재시도하지 않고 실패 |
| `NOT_FOUND_KAKAO_SENDER` | 404 | 카카오 sender를 사용할 수 없는 owner 유형 또는 컨텍스트다. | 재시도하지 않고 실패 |
| `INTERNAL_SERVER_ERROR` | 500 | 예상하지 못한 예외다. | 서버 로그 확인 후 재시도 판단 |

## Package Guidance

- 패키지 사용자는 `message`가 아니라 `code` 기준으로 예외를 해석해야 한다.
- `SendGoException::context()`에서 최소한 `status`, `error_code`, `api_version`, `endpoint`, `body`를 확인할 수 있다.
- `SENDGO_API_VERSION` 기본값은 `v1`이며, `v2`를 사용할 서비스만 명시적으로 `v2`로 설정한다.
- `v2`에서 토큰 재발급 재시도는 최대 1회만 수행한다.
- 인증 실패 시 바로 토큰을 삭제하지 않고, 먼저 중앙 캐시의 최신 토큰을 재사용한 뒤 필요한 경우에만 cache lock으로 단일 worker가 재발급한다.
- 여러 queue worker 환경에서는 `redis`, `memcached`, `database`처럼 공유 캐시와 lock을 지원하는 store를 사용해야 한다.
- 같은 요청을 다시 보내도 해결되지 않는 `403`, `404`, `422` 코드는 대부분 설정 또는 payload 문제다.

## Related Files

- `src/SendGo.php`
- `src/Exceptions/SendGoException.php`
- `src/config/sendgo.php`

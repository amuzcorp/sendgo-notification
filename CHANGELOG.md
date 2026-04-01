# Changelog

모든 주요 변경 사항은 이 파일에 기록됩니다.

## [Unreleased]

## [1.2.0] - 2026-04-01

### Added
- `MessageAbstract::toMany()` 추가
- `README.md`에 `to()` / `toMany()` 사용 정책 문서 추가
- `examples/AlimTalk.md`에 직접 서비스 호출 기반 다건 수신자 발송 예제 추가
- `examples/FriendTalk.md`에 직접 서비스 호출 기반 다건 수신자 발송 예제 추가
- `examples/SMS.md`에 직접 서비스 호출 기반 다건 수신자 발송 예제 추가

### Changed
- `MessageAbstract::to()`를 단일 수신자 전용으로 변경
- Laravel Notification 채널에서 `toMany()` 사용 시 `MULTIPLE_RECIPIENTS_NOT_SUPPORTED_IN_NOTIFICATION` 예외가 발생하도록 변경

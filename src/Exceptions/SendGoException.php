<?php

namespace Techigh\SendgoNotification\Exceptions;

use Exception;

class SendGoException extends Exception
{
    private array $ctx;

    public function __construct(string $message = '', array $context = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->ctx = $context;
    }

    public function context(): array
    {
        return $this->ctx;
    }

    public static function fromResponse(
        int $status,
        array $body,
        string $endpoint,
        string $apiVersion
    ): static {
        $errorCode = $body['code'] ?? null;
        $errorMessage = $body['message'] ?? 'Unknown error';

        $message = "HTTP {$status}";
        if ($errorCode !== null) {
            $message .= " [{$errorCode}]";
        }
        $message .= " {$errorMessage}";

        return new static($message, [
            'status'      => $status,
            'body'        => $body,
            'endpoint'    => $endpoint,
            'api_version' => $apiVersion,
            'error_code'  => $errorCode,
        ]);
    }

    public static function tokenFailed(
        int $status,
        array $body,
        string $endpoint,
        string $apiVersion
    ): static {
        $errorCode = $body['code'] ?? null;
        $errorMessage = $body['message'] ?? 'Unknown error';

        $message = "SendGo token request failed. HTTP {$status}";
        if ($errorCode !== null) {
            $message .= " [{$errorCode}]";
        }
        $message .= " {$errorMessage}";

        return new static($message, [
            'status'      => $status,
            'body'        => $body,
            'endpoint'    => $endpoint,
            'api_version' => $apiVersion,
            'error_code'  => $errorCode,
        ]);
    }
}

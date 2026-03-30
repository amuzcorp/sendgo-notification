<?php

namespace Techigh\SendgoNotification;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Techigh\SendgoNotification\Contracts\SendGoAttributeInterface;
use Techigh\SendgoNotification\Exceptions\SendGoException;

class SendGo
{
    protected string $url;
    protected string $endpoint;
    protected string $apiVersion = 'v1';
    protected ?string $uri = null;

    protected array $headers;

    protected string $accessKey;
    protected string $secretKey;
    protected ?string $senderKey;
    protected ?string $kakaoSenderKey;

    protected SendGoAttributeInterface $attribute;

    protected ?string $token = null;

    private const ALLOWED_VERSIONS = ['v1', 'v2'];

    private const V2_NO_REFRESH_CODES = [
        'INVALID_AUTH_HEADER',
        'INVALID_BASIC_AUTH',
        'INVALID_BASIC_AUTH_PAYLOAD',
        'INVALID_ACCESS_KEY',
        'INVALID_SECRET_KEY',
        'ACCESS_KEY_NOT_APPROVED',
        'TEAM_REQUIRED_FOR_KAKAO',
        'IP_NOT_ALLOWED',
        'INVALID_SENDER_KEY',
        'INVALID_KAKAO_SENDER_KEY',
    ];

    public function __construct()
    {
        $this->initializeKeys()
            ->initializeSenderKeys()
            ->initializeApiVersion()
            ->initializeApiUrl()
            ->initializeHeaders()
            ->issueToken();
    }

    /* -----------------------------------------------------------------
     | Token
     |-----------------------------------------------------------------*/

    protected function issueToken(): void
    {
        if (!$this->validateKeys()) {
            throw new SendGoException('Empty Access Key');
        }

        $cacheKey = $this->getTokenCacheKey();

        $this->token = Cache::remember($cacheKey, now()->addMinutes(50), function () {
            return $this->requestNewToken();
        });

        if (empty($this->token)) {
            throw new SendGoException('Failed to get token from cache');
        }
    }

    protected function requestNewToken(): string
    {
        $tokenEndpoint = $this->buildTokenEndpoint();

        $response = Http::timeout(10)->withHeaders([
            'Content-Type'  => $this->headers['Content-Type'],
            'Authorization' => $this->makeBasicAuthorization(),
        ])->post($tokenEndpoint);

        $body = $response->json() ?? [];

        if ($response->failed() || empty($body['data']['token'])) {
            throw SendGoException::tokenFailed(
                $response->status(),
                $body,
                'token',
                $this->apiVersion
            );
        }

        return $body['data']['token'];
    }

    protected function forceRefreshToken(): void
    {
        $cacheKey = $this->getTokenCacheKey();
        Cache::forget($cacheKey);
        $this->token = $this->requestNewToken();
        Cache::put($cacheKey, $this->token, now()->addMinutes(50));
    }

    protected function getTokenCacheKey(): string
    {
        return 'sendgo_token:' . $this->apiVersion . ':' . md5($this->accessKey . $this->secretKey);
    }

    protected function validateToken(): bool
    {
        return !empty($this->token);
    }

    /* -----------------------------------------------------------------
     | HTTP Client
     |-----------------------------------------------------------------*/

    protected function client()
    {
        if (!$this->validateToken()) {
            throw new SendGoException('Invalid Bearer Authorization: token not found.');
        }

        return Http::withHeaders([
            'Content-Type'  => $this->headers['Content-Type'],
            'Authorization' => $this->makeBearerAuthorization(),
        ]);
    }

    /* -----------------------------------------------------------------
     | Send
     |-----------------------------------------------------------------*/

    protected function performSend(string $url, array $body): array
    {
        $response = $this->client()->post($url, $body);

        if ($this->shouldRefreshToken($response)) {
            $this->forceRefreshToken();
            $response = $this->client()->post($url, $body);
        }

        if ($response->failed()) {
            $endpointName = basename(parse_url($url, PHP_URL_PATH) ?? $url);
            throw SendGoException::fromResponse(
                $response->status(),
                $response->json() ?? [],
                $endpointName,
                $this->apiVersion
            );
        }

        return $response->json() ?? [];
    }

    protected function shouldRefreshToken($response): bool
    {
        if (!in_array($response->status(), [401, 403])) {
            return false;
        }

        if ($this->apiVersion === 'v2') {
            $code = ($response->json() ?? [])['code'] ?? null;
            if ($code !== null && in_array($code, self::V2_NO_REFRESH_CODES)) {
                return false;
            }
            return true;
        }

        // v1: 401/403이면 무조건 재발급
        return true;
    }

    /* -----------------------------------------------------------------
     | Authorization
     |-----------------------------------------------------------------*/

    protected function makeBasicAuthorization(): string
    {
        return 'Basic ' . base64_encode(
            sprintf('%s:%s', $this->accessKey, $this->secretKey)
        );
    }

    protected function makeBearerAuthorization(): string
    {
        if ($this->apiVersion === 'v2') {
            return 'Bearer ' . $this->token;
        }

        return 'Bearer ' . base64_encode($this->token);
    }

    /* -----------------------------------------------------------------
     | Initialize
     |-----------------------------------------------------------------*/

    protected function initializeApiVersion(): static
    {
        $version = config('sendgo.api_version', 'v1');
        if (!in_array($version, self::ALLOWED_VERSIONS)) {
            throw new SendGoException(
                "Invalid API version: {$version}. Allowed values are: v1, v2.",
                ['error_code' => 'INVALID_API_VERSION']
            );
        }
        $this->apiVersion = $version;
        return $this;
    }

    protected function initializeHeaders(): static
    {
        $this->headers = [
            'Content-Type' => config('sendgo.content_type'),
        ];
        return $this;
    }

    protected function initializeApiUrl(): static
    {
        $this->endpoint = config('sendgo.url');
        $this->url = $this->endpoint . '/api';
        return $this;
    }

    protected function initializeSenderKeys(): static
    {
        $this->senderKey = config('sendgo.sms_sender_key');
        $this->kakaoSenderKey = config('sendgo.kakao_sender_key');
        return $this;
    }

    protected function initializeKeys(): static
    {
        $this->accessKey = config('sendgo.access_key');
        $this->secretKey = config('sendgo.secret_key');
        return $this;
    }

    protected function validateKeys(): bool
    {
        return !empty($this->accessKey) && !empty($this->secretKey);
    }

    /* -----------------------------------------------------------------
     | Endpoint Builders
     |-----------------------------------------------------------------*/

    protected function buildTokenEndpoint(): string
    {
        return $this->url . "/{$this->apiVersion}/token";
    }

    /* -----------------------------------------------------------------
     | Utils
     |-----------------------------------------------------------------*/

    protected function start(string $value, string $prefix = '/'): string
    {
        return Str::start($value, $prefix);
    }
}

<?php

namespace Techigh\SendgoNotification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Techigh\SendgoNotification\Contracts\SendGoAttributeInterface;
use Techigh\SendgoNotification\Exceptions\SendGoException;

class SendGo
{
    protected string $url;
    protected string $endpoint;
    protected ?string $uri = null;

    protected array $headers;

    protected string $accessKey;
    protected string $secretKey;
    protected ?string $senderKey;
    protected ?string $kakaoSenderKey;

    protected SendGoAttributeInterface $attribute;

    protected ?string $token = null;

    public function __construct()
    {
        $this->initializeKeys()
            ->initializeSenderKeys()
            ->initializeApiUrl()
            ->initializeHeaders()
            ->issueToken();
    }

    /* -----------------------------------------------------------------
     | Token
     |-----------------------------------------------------------------*/

    /**
     * @throws SendGoException
     */
    protected function issueToken(): void
    {
        if (!$this->validateKeys()) {
            throw new SendGoException('Empty Access Key');
        }

        $response = Http::withHeaders([
            'Content-Type'  => $this->headers['Content-Type'],
            'Authorization' => $this->makeBasicAuthorization(),
        ])->post($this->url . '/v1/token');

        $body = $response->json();

        if ($response->failed() || empty($body['data']['token'])) {
            throw new SendGoException($body['code'] ?? 'Token request failed');
        }

        $this->token = $body['data']['token'];
    }

    protected function validateToken(): bool
    {
        return !empty($this->token);
    }

    /* -----------------------------------------------------------------
     | HTTP Client
     |-----------------------------------------------------------------*/

    /**
     * Bearer 인증이 포함된 새 Http Client 반환
     *
     * @throws SendGoException
     */
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
        return 'Bearer ' . base64_encode($this->token);
    }

    /* -----------------------------------------------------------------
     | Initialize
     |-----------------------------------------------------------------*/

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
     | Utils
     |-----------------------------------------------------------------*/

    protected function start(string $value, string $prefix = '/'): string
    {
        return Str::start($value, $prefix);
    }
}

<?php

namespace Techigh\SendgoNotification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Techigh\SendgoNotification\Contracts\SendGoAttributeInterface;
use Techigh\SendgoNotification\Exceptions\SendGoException;

class SendGo
{
    protected $client;
    protected string $url;
    protected string $uri;
    protected string $endpoint;

    protected array $headers;
    protected string $accessKey;
    protected string $secretKey;
    protected string|null $senderKey;
    protected string|null $kakaoSenderKey;
    protected SendGoAttributeInterface $attribute;

    protected string $token;


    public function __construct()
    {
        $this->initializeKeys()
            ->initializeSenderKeys()
            ->initializeApiUrl()
            ->initializeHeaders()
            ->initializeHttp()
            ->token()
            ->replaceHeaders();
    }

    private function replaceHeaders(): static
    {
        $this->client->replaceHeaders(
            $this->headers + [
                'Authorization' => $this->makeBearerAuthorization()
            ]);
        return $this;
    }

    private function makeBearerAuthorization(): string
    {
        return 'Bearer ' . base64_encode($this->token);
    }

    /**
     * @throws SendGoException
     */
    private function token(): static
    {
        if (!$this->validateKeys()) {
            throw new SendGoException('Empty Access Key');
        }

        try {
            $response = $this->client->replaceHeaders(
                $this->headers + [
                    'Authorization' => $this->makeBasicAuthorization()
                ]
            )->post($this->url . '/v1/token');
        } catch (\Exception $e) {
            throw new SendGoException($e->getMessage());
        }
        $body = json_decode($response->body(), true);
        if ($response->failed()) {
            throw new SendGoException($body['code']);
        } else {
            $this->token = $body['data']['token'];
        }
        return $this;
    }

    protected function validateKeys(): bool
    {
        return !empty($this->accessKey) && !empty($this->secretKey);
    }

    private function makeBasicAuthorization(): string
    {
        return 'Basic ' . base64_encode(sprintf('%s:%s', $this->accessKey, $this->secretKey));
    }

    /**
     * @return $this
     */
    protected function initializeHttp(): static
    {
        $this->client = Http::withHeaders($this->headers);
        return $this;
    }

    /**
     * @return $this
     */
    private function initializeHeaders(): static
    {
        $this->headers = [
            'Content-Type' => config('sendgo.content_type'),
        ];
        return $this;
    }

    /**
     * @return $this
     */
    private function initializeApiUrl(): static
    {
        $this->endpoint = config('sendgo.endpoint');
        $this->url = $this->endpoint;
        return $this;
    }

    private function initializeSenderKeys(): static
    {
        $this->senderKey = config('sendgo.sms_sender_key');
        $this->kakaoSenderKey = config('sendgo.kakao_sender_key');
        return $this;
    }

    private function initializeKeys(): static
    {
        $this->accessKey = config('sendgo.access_key');
        $this->secretKey = config('sendgo.secret_key');
        return $this;
    }

    protected function validateToken(): bool
    {
        return !empty($this->token);
    }

    /**
     * @param string $value
     * @param string $prefix
     * @return string
     */
    protected function start(string $value, string $prefix = '/'): string
    {
        return Str::start($value, $prefix);
    }
}

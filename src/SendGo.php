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


    public function __construct()
    {
        $this->initializeKeys()
            ->initializeSenderKeys()
            ->initializeApiUrl()
            ->initializeHeaders()
            ->initializeHttp()
            ->debug();
    }

    private function debug(): void
    {
        if (config('sendgo.debug') == 'true') {
            $this->client->replaceHeaders(
                $this->headers + [
                    'debug' => true
                ]);
            $this->client->withOptions(['verify' => false]);
        }
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
            'Authorization' => $this->makeBasicAuthorization(),
            'senderKey' => $this->senderKey,
            'kakaoSenderKey' => $this->kakaoSenderKey
        ];
        return $this;
    }

    private function makeBasicAuthorization(): string
    {
        return 'Basic ' . base64_encode(sprintf('%s:%s', $this->accessKey, $this->secretKey));
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
        $this->senderKey = config('sendgo.sms.sender_key');
        $this->kakaoSenderKey = config('sendgo.kakao.sender_key');
        return $this;
    }

    private function initializeKeys(): static
    {
        $this->accessKey = config('sendgo.access_key');
        $this->secretKey = config('sendgo.secret_key');
        return $this;
    }

    protected function validateKeys(): bool
    {
        return !empty($this->accessKey) && !empty($this->secretKey);
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

    /**
     * @throws SendGoException
     */
    protected function handleException($response)
    {
        $body = json_decode($response->body(), true);
        Log::debug($body);
        if ($response->status() === 422) {
            $errors = $body['errors'];
            $errorsKey = array_keys($errors);
            throw new SendGoException(implode(', ', $errorsKey), $body['code']);
        }
        throw new SendGoException($body['code']);
    }
}

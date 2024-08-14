<?php

namespace Techigh\SendgoNotification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Techigh\SendgoNotification\Contracts\SendGoAttributeInterface;
use Techigh\SendgoNotification\Exceptions\SendGoException;

class SendGo
{
    protected $client;
    protected string $url;
    protected string $uri;
    protected string $endpoint;
    protected bool $debug = false;

    protected array $headers;
    protected string $accessKey;
    protected string $secretKey;
    protected string $senderKey;
    protected SendGoAttributeInterface $attribute;


    public function __construct()
    {
        $this->initializeKeys()
            ->initializeApiUrl()
            ->initializeHeaders()
            ->initializeHttp()
            ->debug();
    }

    private function debug(): void
    {
        if (config('sendgo.debug') == 'true') {
            $this->debug = true;
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
//            'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
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

    private function initializeKeys(): static
    {
        $this->accessKey = config('sendgo.access_key');
        $this->secretKey = config('sendgo.secret_key');
        return $this;
    }

    protected function validateKeys(): bool
    {
        return true;
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
        if ($response->status() === 422) {
            $errors = $body['errors'];
            $errorsKey = array_keys($errors);
            throw new SendGoException(implode(', ', $errorsKey), $body['code']);
        }
        throw new SendGoException($body['message'], $body['code']);
    }
}

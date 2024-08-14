<?php

namespace Techigh\SendgoNotification\Attributes\Alim;

use Illuminate\Support\Facades\Log;
use Techigh\SendgoNotification\Contracts\SendGoAttributeInterface;
use Techigh\SendgoNotification\Exceptions\SendGoException;
use Techigh\SendgoNotification\SendGo;

class AlimTalk extends SendGo implements SendGoAttributeInterface
{
    protected string $kakaoSenderKey;

    public function __construct()
    {
        parent::__construct();
        $this->initializeSenderKey()
            ->initializeUri();
    }


    public function initializeUri(): static
    {
        $this->uri = '/notice';
        return $this;
    }

    public function initializeSenderKey(): static
    {
        $this->senderKey = config('sendgo.sms.sender_key');
        $this->kakaoSenderKey = config('sendgo.kakao.sender_key');
        return $this;
    }

    /**
     * @throws SendGoException
     */
    public function send(array $params): void
    {
        if (!$this->validateKeys()) {
//            throw SensException::InvalidNCPTokens('NCP tokens are invalid.');
        }
        $body = $params + [
                'kakaoSenderKey' => $this->kakaoSenderKey,
                'senderKey' => $this->senderKey,
                'debug' => $this->debug
            ];
        Log::debug($this->createEndPoint('send'));
        Log::debug(json_encode($body));
        $response = $this->client->post($this->createEndPoint('send'), $body);
        Log::debug($response);
        if ($response->failed()) {
            $this->handleException($response);
        }
    }

    public function createEndpoint(?string $endpoint, bool $withUri = true): string
    {
        if ($withUri) {
            return $this->url . $this->uri . $this->start($endpoint);
        }
        return $this->url . $this->start($endpoint);
    }
}

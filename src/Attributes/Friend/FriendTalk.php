<?php

namespace Techigh\SendgoNotification\Attributes\Friend;

use Illuminate\Support\Facades\Log;
use Techigh\SendgoNotification\Contracts\SendGoAttributeInterface;
use Techigh\SendgoNotification\Exceptions\SendGoException;
use Techigh\SendgoNotification\SendGo;

class FriendTalk extends SendGo implements SendGoAttributeInterface
{

    public function __construct()
    {
        parent::__construct();
        $this->initializeUri();
    }


    public function initializeUri(): static
    {
        $this->uri = '/v1/friends';
        return $this;
    }

    /**
     * @throws SendGoException
     */
    public function send(array $params): void
    {
        if (!$this->validateToken()) {
            throw new SendGoException('Empty Token');
        }
        try {
            $body = $params + [
                    'kakaoSenderKey' => $this->kakaoSenderKey,
                    'senderKey' => $this->senderKey,
                ];
            $response = $this->client->post($this->createEndPoint('send'), $body);
        } catch (\Exception $e) {
            throw new SendGoException($e);
        }
        $body = json_decode($response->body(), true);
        if ($response->failed()) {
            throw new SendGoException($body['code']);
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

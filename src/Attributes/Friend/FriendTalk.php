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
        $this->uri = '/v1/notification/friend';
        return $this;
    }

    /**
     * @throws SendGoException
     */
    public function send(array $params): void
    {
        if (!$this->validateKeys()) {
            throw new SendGoException('Invalid Access Key, Secret Key');
        }
        $body = $params + [
                'kakaoSenderKey' => $this->kakaoSenderKey,
                'senderKey' => $this->senderKey,
            ];
        $response = $this->client->post($this->createEndPoint('send'), $body);
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

<?php

namespace Techigh\SendgoNotification\Attributes\Friend;

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
        $this->uri = "/{$this->apiVersion}/friends";
        return $this;
    }

    /**
     * @throws SendGoException
     */
    public function send(array $params): void
    {
        $body = $params + [
            'kakaoSenderKey' => $this->kakaoSenderKey,
            'senderKey'      => $this->senderKey,
        ];
        $this->performSend($this->createEndpoint('send'), $body);
    }

    public function createEndpoint(?string $endpoint, bool $withUri = true): string
    {
        if ($withUri) {
            return $this->url . $this->uri . $this->start($endpoint);
        }
        return $this->url . $this->start($endpoint);
    }
}

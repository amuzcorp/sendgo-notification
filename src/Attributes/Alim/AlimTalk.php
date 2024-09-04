<?php

namespace Techigh\SendgoNotification\Attributes\Alim;

use Illuminate\Support\Facades\Log;
use Techigh\SendgoNotification\Contracts\SendGoAttributeInterface;
use Techigh\SendgoNotification\Exceptions\SendGoException;
use Techigh\SendgoNotification\SendGo;

class AlimTalk extends SendGo implements SendGoAttributeInterface
{

    public function __construct()
    {
        parent::__construct();
        $this->initializeUri();
    }


    public function initializeUri(): static
    {
        $this->uri = '/v1/notification/notice';
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
        try {
            $body = $params + [
                    'kakaoSenderKey' => $this->kakaoSenderKey,
                    'senderKey' => $this->senderKey,
                ];
            $this->client->post($this->createEndPoint('send'), $body);
        } catch (\Exception $e) {
            throw new SendGoException($e);
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

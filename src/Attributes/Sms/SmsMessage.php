<?php

namespace Techigh\SendgoNotification\Attributes\Sms;

use Techigh\SendgoNotification\Contracts\MessageAbstract;

class SmsMessage extends MessageAbstract
{
    private string $campaignType = 'MESSAGE'; // MESSAGE | ADVERTISE | ELECTION
    private string $messageType = 'SMS'; // SMS | LMS | MMS
    private string|null $title = null; //
    private string $content;
    private array $images = [];


    /**
     * @breif Required
     * @param string $content
     * @return $this
     */
    public function content(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @brief Optional
     * @param string|null $title
     * @return $this
     */
    public function title(string $title = null): static
    {
        $this->title = $title;
        return $this;
    }


    /**
     * @breif Required | Default Value = 'SMS'
     * @param string $messageType
     * @return $this
     */
    public function messageType(string $messageType): static
    {
        $this->messageType = $messageType;
        return $this;
    }


    /**
     * @breif Required | Default Value = 'MESSAGE'
     * @param string $campaignType
     * @return $this
     */
    public function campaignType(string $campaignType): static
    {
        $this->campaignType = $campaignType;
        return $this;
    }

    /**
     * @breif Optional | Default Value = []
     * @param array $images
     * @return $this
     */
    public function images(array $images = []): static
    {
        $this->images = $images;
        return $this;
    }

    public function toArray(): array
    {
        return [
            "messageCampaignType" => $this->campaignType,
            "messageTranType" => $this->messageType,
            "messageTranScheduleType" => $this->scheduleType,
            "messageTranAt" => $this->at,
            "messageTranSubject" => $this->title,
            "messageTranMsg" => $this->content,
            "images" => $this->images,
            "receivers" => $this->to
        ];
    }
}

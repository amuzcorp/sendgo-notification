<?php

namespace Techigh\SendgoNotification\Attributes\Sms;

use Techigh\SendgoNotification\Contracts\MessageAbstract;

class SmsMessage extends MessageAbstract
{
    private string $campaignType = 'MESSAGE'; // MESSAGE | ADVERTISE | ELECTION
    private string $messageType = 'SMS'; // SMS | LMS | MMS
    private string|null $subject = null; //
    private string $content;
    private array $files = [];


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
     * @param string|null $subject
     * @return $this
     */
    public function subject(string $subject = null): static
    {
        $this->subject = $subject;
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
     * @param array $files
     * @return $this
     */
    public function files(array $files = []): static
    {
        $this->files = $files;
        return $this;
    }

    public function toArray(): array
    {
        return [
            "campaignType" => $this->campaignType,
            "messageType" => $this->messageType,
            "scheduleType" => $this->scheduleType,
            "at" => $this->at,
            "subject" => $this->subject,
            "content" => $this->content,
            "files" => $this->files,
            "receivers" => $this->to
        ];
    }


}

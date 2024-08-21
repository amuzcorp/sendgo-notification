<?php

namespace Techigh\SendgoNotification\Attributes\Alim;

use Techigh\SendgoNotification\Contracts\MessageAbstract;

class AlimTalkMessage extends MessageAbstract
{
    private string $templateCode;
    private string|null $smsTitle = null;
    private string|null $smsContent = null;
    private string $replaceSms = 'N';


    public function templateCode(string $templateCode): static
    {
        $this->templateCode = $templateCode;
        return $this;
    }

    public function replaceSms(string $replaceSms): static
    {
        $this->replaceSms = $replaceSms;
        return $this;
    }

    public function smsTitle(string $smsTitle): static
    {
        $this->smsTitle = $smsTitle;
        return $this;
    }

    public function smsContent(string $smsContent): static
    {
        $this->smsContent = $smsContent;
        return $this;
    }


    public function toArray(): array
    {
        if ($this->replaceSms == 'N') {
            $this->smsContent = null;
            $this->smsTitle = null;
        }
        return [
            'at' => $this->at,
            "scheduleType" => $this->scheduleType,
            'templateCode' => $this->templateCode,
            'replaceSms' => $this->replaceSms,
            'smsSubject' => $this->smsTitle,
            'smsContent' => $this->smsContent,
            'receivers' => $this->to,
        ];
    }
}

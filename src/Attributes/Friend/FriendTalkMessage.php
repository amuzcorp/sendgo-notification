<?php

namespace Techigh\SendgoNotification\Attributes\Friend;

use Techigh\SendgoNotification\Contracts\MessageAbstract;

class FriendTalkMessage extends MessageAbstract
{
    private array $buttons = [];
    private string $type = 'FT'; // FT | FI | FW
    private string $wide = 'N'; // Y | N
    private string $adult = 'N'; // Y | N
    private string $adFlag = 'Y'; // Y | N
    private string $content;
    private string|null $header = null;
    private $image = null;
    private string|null $imageUrl = null;
    private string|null $imageLink = null;
    private string|null $smsTitle = null;
    private string|null $smsContent = null;
    private string $replaceSms = 'N';


    public function content(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function buttons(array $buttons): static
    {
        $this->buttons = $buttons;
        return $this;
    }

    public function header(string $header): static
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @brief Image file / An image file or an image URL, one of which is required.
     * @param $image
     * @return $this
     */
    public function image($image): static
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @brief Image URL link
     * @param string $imageLink
     * @return $this
     */
    public function imageLink(string $imageLink): static
    {
        $this->imageLink = $imageLink;
        return $this;
    }

    /**
     * @brief Image URL / An image file or an image URL, one of which is required.
     * @param string $imageUrl
     * @return $this
     */
    public function imageUrl(string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;
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
            'messageTranAt' => $this->at,
            "messageTranScheduleType" => $this->scheduleType,
            'type' => $this->type,
            'templateContent' => $this->content,
            'buttons' => $this->buttons,
            'image' => $this->image,
            'imageUrl' => $this->imageUrl,
            'imageLink' => $this->imageLink,
            'adFlag' => $this->adFlag,
            'wide' => $this->wide,
            'adult' => $this->adult,
//            'additionalContent',
            'header' => $this->header,
//            'carousel',
            'replaceSms' => $this->replaceSms,
            'smsSubject' => $this->smsTitle,
            'smsContent' => $this->smsContent,
            'receivers' => $this->to,
        ];
    }
}

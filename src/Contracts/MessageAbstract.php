<?php

namespace Techigh\SendgoNotification\Contracts;

abstract class MessageAbstract
{
    protected $at = null;
    protected array $to = [];
    protected bool $multipleRecipients = false;
    protected string $scheduleType = 'DIRECTLY'; // DIRECTLY | RESERVED

    static function make(): static
    {
        return new static();
    }


    /**
     * @breif Required for bulk sending outside Laravel Notification channels
     * @param array $to
     * @return $this
     */
    public function toMany(array $to): static
    {
        if (!array_is_list($to) || (isset($to[0]) && !is_array($to[0]))) {
            throw new \InvalidArgumentException('toMany() expects a list of recipient arrays.');
        }

        $this->multipleRecipients = true;
        $this->to = $to;

        return $this;
    }

    public function hasMultipleRecipients(): bool
    {
        return $this->multipleRecipients;
    }

    public function to(array $to): static
    {
        if (array_is_list($to) && isset($to[0]) && is_array($to[0])) {
            throw new \InvalidArgumentException('Use toMany() for multiple recipients.');
        }

        $this->multipleRecipients = false;
        $this->to = [$to];

        return $this;
    }

    /**
     * @breif Optional | Default Value = now
     * @param $at
     * @return $this
     */
    public function at($at = null): static
    {
        $this->at = $at;
        return $this;
    }

    /**
     * @breif Required | Default Value = 'DIRECTLY'
     * @param string $scheduleType
     * @return $this
     */
    public function scheduleType(string $scheduleType): static
    {
        $this->scheduleType = $scheduleType;
        return $this;
    }
}

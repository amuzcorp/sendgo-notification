<?php

namespace Techigh\SendgoNotification\Contracts;

abstract class MessageAbstract
{
    protected $at = null;
    protected array $to;
    protected string $scheduleType = 'DIRECTLY'; // DIRECTLY | RESERVED

    static function make(): static
    {
        return new static();
    }

    /**
     * @breif Required
     * @param array|string $to
     * @return $this
     */
    public function to(array|string $to): static
    {
        if (empty($to)) {
            echo "수신 정보가 없습니다.";
        }
        if (is_array($to) && !array_key_exists('contact', $to)) $to = [$to];
        else $to = [['contact' => $to]];
        $this->to = $to;
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

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
     * @param array $to
     * @return $this
     */
    public function to(array $to): static
    {
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

<?php

namespace Techigh\SendgoNotification\Contracts;

interface SendGoAttributeInterface
{
    public function send(array $params);

    public function initializeUri();

    public function initializeSenderKey();
}

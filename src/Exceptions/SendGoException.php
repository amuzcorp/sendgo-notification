<?php

namespace Techigh\SendgoNotification\Exceptions;

use Exception;

const EXCEPTION_CODES = [
    -1 => 'Validation Exception',
    -2 => 'Invalid Sender Key'
];
class SendGoException extends Exception
{

}

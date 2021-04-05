<?php

namespace Lampdev\Hubspot;

use Exception;
use Throwable;

class HubspotException extends Exception
{
    const EXC_MESSAGE_PREFIX = 'HubSpot Integration Exception: ';

    public function __construct(
        string $message,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct(
            self::EXC_MESSAGE_PREFIX . $message,
            $code,
            $previous
        );
    }
}

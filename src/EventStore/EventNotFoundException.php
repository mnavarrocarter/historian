<?php

namespace Historian\EventStore;

use Exception;
use Throwable;

/**
 * Class EventNotFoundException
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class EventNotFoundException extends Exception
{
    public function __construct(string $eventId)
    {
        parent::__construct(sprintf('The event with id "%s" does not exist', $eventId));
    }
}
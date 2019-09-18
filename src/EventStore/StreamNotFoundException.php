<?php

namespace Historian\EventStore;

use Exception;
use RuntimeException;
use Throwable;

/**
 * Class StreamNotFoundException
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class StreamNotFoundException extends RuntimeException
{
    /**
     * StreamNotFoundException constructor.
     * @param string $streamName
     */
    public function __construct(string $streamName)
    {
        parent::__construct(sprintf('The event stream "%s" does not exist', $streamName));
    }
}
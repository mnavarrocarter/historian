<?php
declare(strict_types=1);

namespace Historian\HttpEventStore;

use Historian\EventStore\ReadOnlyEventStore;
use Historian\HttpEventStore\ContentType\ContentTypeMaker;

/**
 * Class HttpEventStore
 * @package Historian\HttpEventStore
 */
class HttpEventStore
{
    /**
     * @var ReadOnlyEventStore
     */
    private $eventStore;
    /**
     * @var ContentTypeMaker
     */
    private $maker;

    /**
     * HttpEventStore constructor.
     * @param ReadOnlyEventStore $eventStore
     * @param ContentTypeMaker $maker
     */
    public function __construct(ReadOnlyEventStore $eventStore, ContentTypeMaker $maker)
    {
        $this->eventStore = $eventStore;
        $this->maker = $maker;
    }
}
<?php

namespace Historian\EventStore;

use Historian\Event\EventStream;

/**
 * Class ReadOnlyEventStore
 *
 * A read-only Event Store contract.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface ReadOnlyEventStore
{
    /**
     * @param string $streamName
     * @return bool
     */
    public function hasStream(string $streamName): bool;

    /**
     * @param string $streamName
     * @return EventStream
     * @throws StreamNotFoundException
     */
    public function load(string $streamName): EventStream;
}
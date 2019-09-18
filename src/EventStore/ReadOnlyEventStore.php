<?php

namespace Historian\EventStore;

/**
 * Class ReadOnlyEventStore
 *
 * Description of what this class does goes here.
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
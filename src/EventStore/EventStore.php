<?php

namespace Historian\EventStore;

/**
 * Interface EventStore
 *
 * Defines the contract for an Event Store.
 *
 * An Event Store saves streams of events using a persistence layer.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface EventStore extends ReadOnlyEventStore
{
    /**
     * @param string $streamName
     * @param EventStream $events
     */
    public function append(string $streamName, EventStream $events): void;

    /**
     * @param string $streamName
     */
    public function delete(string $streamName): void;
}
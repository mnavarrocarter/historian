<?php

namespace Historian\EventStore;

/**
 * Interface EventStoreDecorator
 *
 * This is the contract for a decorated Event Store.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface EventStoreDecorator extends EventStore
{
    /**
     * @return EventStore
     */
    public function getInnerEventStore(): EventStore;
}
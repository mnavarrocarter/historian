<?php

namespace Historian\Projector;

use Historian\Event\Event;

/**
 * Interface Projector
 *
 * A projector that can be run every x seconds or time.
 *
 * If passed an implementation of PersistentState, can project
 * only the events that has not processed.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface Projector
{
    /**
     * Projects an event.
     *
     * @param Event $event
     * @throws ProjectionFailedException when projection fails for some reason
     * @return void
     */
    public function project(Event $event): void;
}
<?php

namespace Historian\Projector;

use Historian\EventStore\EventStore;

/**
 * Interface Projector
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface Projector
{
    /**
     * Returns the projector name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Process the events in a projector
     *
     * @param EventStore $eventStore
     * @param ProjectorTracker $tracker
     * @return void
     */
    public function process(EventStore $eventStore, ProjectorTracker $tracker): void;
}
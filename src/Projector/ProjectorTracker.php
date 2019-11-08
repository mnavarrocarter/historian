<?php

namespace Historian\Projector;

/**
 * Interface PersistentState
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface ProjectorTracker
{
    /**
     * @param string $projectorName
     * @param int $eventNumber
     */
    public function track(string $projectorName, int $eventNumber): void;

    /**
     *
     * @param string $projectorName
     */
    public function reset(string $projectorName): void;

    /**
     * Returns the last tracked event.
     *
     * If no events are tracked for a certain projector, then a -1 MUST be returned.
     *
     * @param string $projectorName
     * @return int|null
     */
    public function lastTrackedEvent(string $projectorName): int;
}
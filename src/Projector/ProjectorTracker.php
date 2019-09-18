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
     * @param string $id
     * @param int $eventNumber
     */
    public function track(string $id, int $eventNumber): void;

    /**
     * @param string $id
     */
    public function reset(string $id): void;

    /**
     * @param string $id
     * @return int
     */
    public function lastTrackedEventId(string $id): int;
}
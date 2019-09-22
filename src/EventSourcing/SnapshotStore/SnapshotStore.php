<?php

namespace Historian\EventSourcing\SnapshotStore;

use Historian\EventSourcing\AggregateRoot;

/**
 * Class SnapshotStore
 *
 * Saves and gets snapshots of an aggregate to avoid replaying all events to recover state.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface SnapshotStore
{
    /**
     * @param AggregateRoot $aggregateRoot
     * @return void
     */
    public function save(AggregateRoot $aggregateRoot): void;

    /**
     * @param string $aggregateId
     * @return bool
     */
    public function has(string $aggregateId): bool;

    /**
     * @param string $aggregateId
     * @return AggregateRoot
     */
    public function get(string $aggregateId): AggregateRoot;
}
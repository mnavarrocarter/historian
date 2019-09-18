<?php

namespace Historian\SnapshotStore;

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
     * @return mixed
     */
    public function save(AggregateRoot $aggregateRoot);

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
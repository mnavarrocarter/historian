<?php

namespace Historian\EventSourcing;

use Cassandra\Aggregate;
use Closure;
use Historian\EventSourcing\SnapshotStore\SnapshotStore;
use Historian\EventStore\EventStore;
use Historian\Event\EventStream;
use Historian\EventStore\StreamNotFoundException;
use Historian\Util\PropertyAccessor;

/**
 * Class AggregateRootRepository
 *
 * This is an aggregate repository that uses an Event Store implementation to
 * reconstitute aggregates.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
final class EventSourcedAggregateRepository implements AggregateRepository
{
    /**
     * @var EventStore
     */
    private $eventStore;
    /**
     * @var PropertyAccessor
     */
    private $accessor;
    /**
     * @var SnapshotStore|null
     */
    private $snapshots;

    /**
     * AggregateRootRepository constructor.
     * @param EventStore $eventStore
     * @param PropertyAccessor $accessor
     * @param SnapshotStore|null $snapshots
     */
    public function __construct(EventStore $eventStore, PropertyAccessor $accessor, SnapshotStore $snapshots = null)
    {
        $this->eventStore = $eventStore;
        $this->accessor = $accessor;
        $this->snapshots = $snapshots;
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    public function saveAggregate(AggregateRoot $aggregateRoot): void
    {
        /** @var EventStream $events */
        $events = $this->accessor->get($aggregateRoot, 'events', AggregateRoot::class);
        $aggregateId = $this->accessor->get($aggregateRoot, 'id', AggregateRoot::class);

        $this->eventStore->append($aggregateId, $events);

        if ($this->snapshots instanceof SnapshotStore) {
            $this->snapshots->save($aggregateRoot);
        }
    }

    /**
     * @param string $aggregateId
     * @return bool
     */
    public function aggregateExists(string $aggregateId): bool
    {
        return $this->eventStore->hasStream($aggregateId);
    }

    /**
     * @param string $aggregateId
     * @return AggregateRoot
     * @throws StreamNotFoundException
     */
    public function findAggregate(string $aggregateId): ?AggregateRoot
    {
        // If stream does not exist, then null.
        if (!$this->eventStore->hasStream($aggregateId)) {
            return null;
        }
        $aggregate = null;
        // If there's a snapshot store and has that aggregateId, then we fetch it.
        if ($this->snapshots instanceof SnapshotStore && $this->snapshots->has($aggregateId)) {
            $aggregate = $this->snapshots->get($aggregateId);
        }

        $events = $this->eventStore->load($aggregateId);

        $recreate = Closure::bind(static function (EventStream $events, AggregateRoot $aggregateRoot = null) {
            /** @noinspection PhpUndefinedMethodInspection */
            return self::reconstituteFromHistory($events, $aggregateRoot);
        }, null, AggregateRoot::class);

        return $recreate($events, $aggregate);
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    public function deleteAggregate(AggregateRoot $aggregateRoot): void
    {
        $this->saveAggregate($aggregateRoot);
        $this->eventStore->delete(
            $this->accessor->get($aggregateRoot, 'id', AggregateRoot::class)
        );
    }
}
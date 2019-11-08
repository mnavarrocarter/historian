<?php
declare(strict_types=1);

namespace Historian\EventStore\Transactional;

use Historian\Event\EventStream;
use Historian\EventStore\EventStore;
use Predis\Client;

/**
 * Wraps an Event Store in a Predis transaction
 *
 * This ensures that appending new events to Redis is an atomic operation.
 *
 * @package Historian\EventStore\Transactional
 */
final class PredisTransactionalEventStore implements EventStore
{
    /**
     * @var Client
     */
    private $predis;
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * PredisTransactionalEventStore constructor.
     * @param EventStore $eventStore
     * @param Client $predis
     */
    public function __construct(EventStore $eventStore, Client $predis)
    {
        $this->eventStore = $eventStore;
        $this->predis = $predis;
    }

    /**
     * @param string $streamName
     * @param EventStream $events
     */
    public function append(string $streamName, EventStream $events): void
    {
        $this->predis->multi();
        $this->eventStore->append($streamName, $events);
        $this->predis->exec();
    }

    /**
     * @param string $streamName
     */
    public function delete(string $streamName): void
    {
        $this->predis->multi();
        $this->eventStore->delete($streamName);
        $this->predis->exec();
    }

    /**
     * @param string $streamName
     * @return bool
     */
    public function hasStream(string $streamName): bool
    {
        return $this->eventStore->hasStream($streamName);
    }

    /**
     * @param string $streamName
     * @return EventStream
     */
    public function load(string $streamName): EventStream
    {
        return $this->eventStore->load($streamName);
    }
}
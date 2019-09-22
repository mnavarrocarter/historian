<?php
declare(strict_types=1);

namespace Historian\EventStore;

use Historian\Event\Event;
use Historian\Event\EventStream;
use Historian\EventStore\StorageDriver\StorageDriver;
use Historian\EventStore\EventSerializer\EventSerializer;
use Iterator;

/**
 * Class LazyEventStream
 *
 * Represents a lazy Event Stream that fetches event data only when iterating.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
final class LazyEventStream implements EventStream
{
    /**
     * @var StorageDriver
     */
    private $driver;
    /**
     * @var EventSerializer
     */
    private $eventSerializer;
    /**
     * @var string
     */
    private $streamName;
    /**
     * @var int
     */
    private $start = 0;
    /**
     * @var int|null
     */
    private $size;

    /**
     * LazyEventStream constructor.
     *
     * @param StorageDriver $driver
     * @param EventSerializer $eventSerializer
     * @param string $streamName
     */
    public function __construct(StorageDriver $driver, EventSerializer $eventSerializer, string $streamName)
    {
        $this->driver = $driver;
        $this->eventSerializer = $eventSerializer;
        $this->streamName = $streamName;
    }

    /**
     * @param int $start
     */
    public function start(int $start): void
    {
        $this->start = $start > 0 ? $start : 0;
    }

    public function size(int $size): void
    {
        $this->size = $size > 0 ? $size : 0;
    }

    /**
     * @return Iterator|EventStream|Event[]
     */
    public function getIterator(): Iterator
    {
        $eventIds = $this->driver->getEventsFromStream(
            $this->streamName,
            $this->start,
            $this->size
        );

        // Then we iterate over each one and yield the key and value
        foreach ($eventIds as $eventId) {
            $event = $this->eventSerializer->deserialize($this->driver->getEventData($eventId))
                ->with('_eventNumber', $this->start);
            yield $event;
            $this->start++;
        }
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->driver->countEventsInStream($this->streamName);
    }
}
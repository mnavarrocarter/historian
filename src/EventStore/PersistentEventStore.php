<?php
declare(strict_types=1);

namespace Historian\EventStore;

use Historian\Event\EventStream;
use Historian\EventStore\EventSerializer\EventSerializer;
use Historian\EventStore\StorageDriver\StorageDriver;
use Historian\EventStore\StreamExtractor\StreamExtractor;

/**
 * Class PersistentEventStore
 *
 * An Event Store implementation that persists the event data using a storage driver.
 *
 * @package Historian\EventStore
 */
final class PersistentEventStore implements EventStore
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
    private $mainStreamName = 'master';
    /**
     * @var StreamExtractor
     */
    private $streamExtractor;

    /**
     * PredisEventStore constructor.
     *
     * @param StorageDriver $driver
     * @param EventSerializer $eventSerializer
     * @param StreamExtractor $streamExtractor
     */
    public function __construct(StorageDriver $driver, EventSerializer $eventSerializer, StreamExtractor $streamExtractor = null)
    {
        $this->driver = $driver;
        $this->eventSerializer = $eventSerializer;
        $this->streamExtractor = $streamExtractor;
    }

    /**
     * @param string $name
     */
    public function changeMainStreamName(string $name): void
    {
        $this->mainStreamName = $name;
    }

    /**
     * @param string $streamName
     * @param EventStream $events
     */
    public function append(string $streamName, EventStream $events): void
    {
        foreach ($events as $event) {
            $this->driver->pushEventToStream($event->eventId(), $this->mainStreamName);
            $this->driver->pushEventToStream($event->eventId(), $streamName);
            if ($this->streamExtractor instanceof  StreamExtractor) {
                foreach ($this->streamExtractor->extractFrom($event) as $stream) {
                    $this->driver->pushEventToStream($event->eventId(), $stream);
                }
            }
            $this->driver->saveEventData($event->eventId(), $this->eventSerializer->serialize($event));
        }
    }

    /**
     * @param string $streamName
     */
    public function delete(string $streamName): void
    {
        $this->driver->deleteStream($streamName);
    }

    /**
     * @param string $streamName
     * @return bool
     */
    public function hasStream(string $streamName): bool
    {
        return $this->driver->streamExists($streamName);
    }

    /**
     * @param string $streamName
     * @return EventStream
     */
    public function load(string $streamName): EventStream
    {
        // First we check if the stream exists.
        if (!$this->driver->streamExists($streamName)) {
            throw new StreamNotFoundException($streamName);
        }

        return new LazyEventStream(
            $this->driver,
            $this->eventSerializer,
            $streamName
        );
    }

    /**
     * @param string $streamName
     * @return bool
     */
    public function streamExists(string $streamName): bool
    {
        return $this->driver->streamExists($streamName);
    }

    /**
     * @param string $streamName
     */
    public function deleteStream(string $streamName): void
    {
        $this->driver->deleteStream($streamName);
    }
}
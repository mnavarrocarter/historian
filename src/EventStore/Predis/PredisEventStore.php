<?php

namespace Historian\EventStore\Predis;

use Historian\EventStore\EventStore;
use Historian\EventStore\EventStream;
use Historian\EventStore\StreamExtractor;
use Historian\EventStore\StreamNotFoundException;
use Historian\Serializer\EventSerializer;

/**
 * Class PredisEventStore
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class PredisEventStore implements EventStore
{
    /**
     * @var PredisClientWrapper
     */
    private $wrapper;
    /**
     * @var EventSerializer
     */
    private $eventSerializer;
    /**
     * @var string
     */
    private $mainStreamName;
    /**
     * @var callable
     */
    private $streamExtractor;

    /**
     * PredisEventStore constructor.
     * @param PredisClientWrapper $wrapper
     * @param EventSerializer $eventSerializer
     * @param string $mainStreamName
     * @param StreamExtractor $streamExtractor
     */
    public function __construct(PredisClientWrapper $wrapper, EventSerializer $eventSerializer, string $mainStreamName = 'master', StreamExtractor $streamExtractor = null)
    {
        $this->wrapper = $wrapper;
        $this->eventSerializer = $eventSerializer;
        $this->mainStreamName = $mainStreamName;
        $this->streamExtractor = $streamExtractor;
    }

    /**
     * @param string $streamName
     * @param EventStream $events
     */
    public function append(string $streamName, EventStream $events): void
    {
        // TODO: Make transactional
        foreach ($events as $event) {
            $this->wrapper->pushEventIdToList($this->mainStreamName, $event->eventId());
            $this->wrapper->pushEventIdToList($streamName, $event->eventId());
            if ($this->streamExtractor !== null) {
                foreach (($this->streamExtractor)($event) as $stream) {
                    $this->wrapper->pushEventIdToList($stream, $event->eventId());
                }
            }
            $this->wrapper->saveEventDataHash($event->eventId(), $this->eventSerializer->serialize($event));
        }
    }

    /**
     * @param string $streamName
     */
    public function delete(string $streamName): void
    {
        $this->wrapper->deleteStreamList($streamName);
    }

    /**
     * @param string $streamName
     * @return bool
     */
    public function hasStream(string $streamName): bool
    {
        return $this->wrapper->eventStreamExists($streamName);
    }

    /**
     * @param string $streamName
     * @return EventStream
     */
    public function load(string $streamName): EventStream
    {
        // First we check if the stream exists.
        if (!$this->wrapper->eventStreamExists($streamName)) {
            throw new StreamNotFoundException($streamName);
        }

        return new PredisEventStream(
            $this->wrapper,
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
        return $this->wrapper->eventStreamExists($streamName);
    }

    /**
     * @param string $streamName
     */
    public function deleteStream(string $streamName): void
    {
        $this->wrapper->deleteStreamList($streamName);
    }
}
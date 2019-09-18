<?php

namespace Historian\EventStore\Predis;

use DateTimeInterface;
use Historian\EventStore\Event;
use Historian\EventStore\EventStream;
use Historian\Serializer\EventSerializer;
use Iterator;

/**
 * Class PredisEventStream
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class PredisEventStream implements EventStream
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
    private $streamName;
    /**
     * @var DateTimeInterface|null
     */
    protected $until;
    /**
     * @var int
     */
    private $start = 0;
    /**
     * @var int|null
     */
    private $size;
    /**
     * @var array
     */
    protected $callables = [];

    /**
     * PredisEventStream constructor.
     *
     * @param PredisClientWrapper $wrapper
     * @param EventSerializer $eventSerializer
     * @param string $streamName
     */
    public function __construct(PredisClientWrapper $wrapper, EventSerializer $eventSerializer, string $streamName)
    {
        $this->wrapper = $wrapper;
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
     * @param DateTimeInterface $time
     */
    public function until(DateTimeInterface $time): void
    {
        $this->until = $time;
    }

    /**
     * @return Iterator|EventStream|Event[]
     */
    public function getIterator(): Iterator
    {
        // We query the elements of the stream with pagination settings.
        $stop = $this->size === null ? -1 : ($this->start + $this->size)-1;

        $elements = $this->wrapper->readStreamEventsIds(
            $this->streamName,
            $this->start,
            $stop
        );

        // Then we iterate over each one and yield the key and value
        foreach ($elements as $eventId) {
            $event = $this->eventSerializer->deserialize($this->wrapper->readEventData($eventId))
                ->with('_eventNumber', $this->start);

            if ($this->until !== null && $event->occurredAt() > $this->until) {
                break;
            }
            foreach ($this->callables as $callable) {
                $event = $callable($event, $this->start);
            }
            yield $event;
            $this->start++;
        }
    }

    /**
     * @param array $state
     * @param callable $reducer
     * @return array
     */
    public function reduce(array $state, callable $reducer): array
    {
        foreach ($this as $event) {
            $state = $reducer($state, $event);
        }
        return $state;
    }

    /**
     * @param callable $callable
     */
    public function apply(callable $callable): void
    {
        $this->callables[] = $callable;
    }
}
<?php

namespace Historian\EventStore;

use DateTimeInterface;
use Iterator;

/**
 * Class InMemoryEventStream
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class InMemoryEventStream implements EventStream
{
    /**
     * @var Event[]
     */
    protected $events = [];
    /**
     * @var int|null
     */
    protected $offset;
    /**
     * @var int|null
     */
    protected $size;
    /**
     * @var DateTimeInterface|null
     */
    protected $until;
    /**
     * @var callable[]
     */
    protected $callables = [];

    /**
     * @param Event $event
     */
    public function push(Event $event): void
    {
        $this->events[] = $event;
    }

    /**
     * @return Event
     */
    public function pop(): Event
    {
        return array_pop($this->events);
    }

    /**
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        if ($this->offset !== null) {
            $this->events = array_slice($this->events, $this->offset, $this->size);
        }
        foreach ($this->events as $key => $event) {
            if ($this->until !== null && $event->occurredAt() > $this->until) {
                break;
            }
            foreach ($this->callables as $callable) {
                $event = $callable($event, $key);
            }
            (yield $key => $event);
        }
    }

    /**
     * @param int $start
     */
    public function start(int $start): void
    {
        $start = $start > 0 ? $start : 0;
        $this->offset = $start;
    }

    /**
     * @param int $size
     */
    public function size(int $size): void
    {
        if ($size > 0) {
            $this->size = $size;
        }
    }

    public function until(DateTimeInterface $time): void
    {
        $this->until = $time;
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

    public function apply(callable $callable): void
    {
        $this->callables[] = $callable;
    }
}
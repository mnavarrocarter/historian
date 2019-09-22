<?php
declare(strict_types=1);

namespace Historian\Event;

use Iterator;

/**
 * Class InMemoryEventStream
 *
 * Represents an Event Stream stored in memory.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
final class InMemoryEventStream implements EventStream
{
    /**
     * @var Event[]
     */
    protected $events;
    /**
     * @var int|null
     */
    protected $offset;
    /**
     * @var int|null
     */
    protected $size;

    /**
     * InMemoryEventStream constructor.
     */
    public function __construct()
    {
        $this->events = [];
    }

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

    public function count(): int
    {
        return count($this->events);
    }
}
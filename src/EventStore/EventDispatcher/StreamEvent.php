<?php

namespace Historian\EventStore\EventDispatcher;

use Historian\Event\EventStream;

/**
 * Class StreamLoaded
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
abstract class StreamEvent
{
    /**
     * @var string
     */
    private $streamName;
    /**
     * @var EventStream
     */
    private $events;

    /**
     * StreamEvent constructor.
     * @param string $streamName
     * @param EventStream $events
     */
    public function __construct(string $streamName, EventStream $events)
    {
        $this->streamName = $streamName;
        $this->events = $events;
    }

    /**
     * @return string
     */
    public function getStreamName(): string
    {
        return $this->streamName;
    }

    /**
     * @return EventStream
     */
    public function getEvents(): EventStream
    {
        return $this->events;
    }

    /**
     * @param EventStream $events
     * @return void
     */
    public function setEvents(EventStream $events): void
    {
        $this->events = $events;
    }
}
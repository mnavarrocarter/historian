<?php

namespace Historian\EventStore\EventDispatcher;

use Historian\EventStore\EventStore;
use Historian\EventStore\EventStoreDecorator;
use Historian\EventStore\EventStream;
use Historian\EventStore\StreamNotFoundException;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventDispatcherEventStore
 *
 * This decorates an Event Store with an Event Dispatcher.
 *
 * This fires two events:
 *
 * -
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class EventDispatcherEventStore implements EventStoreDecorator
{
    /**
     * @var EventStore
     */
    private $eventStore;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * EventEmitterEventStore constructor.
     * @param EventStore $eventStore
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventStore $eventStore, EventDispatcherInterface $dispatcher)
    {
        $this->eventStore = $eventStore;
        $this->dispatcher = $dispatcher;
    }

    public function delete(string $streamName): void
    {
        $this->eventStore->delete($streamName);
    }

    public function hasStream(string $streamName): bool
    {
        return $this->eventStore->hasStream($streamName);
    }

    /**
     * @param string $streamName
     * @return EventStream
     * @throws StreamNotFoundException
     */
    public function load(string $streamName): EventStream
    {
        return $this->eventStore->load($streamName);
    }

    /**
     * @param string $streamName
     * @param EventStream $events
     */
    public function append(string $streamName, EventStream $events): void
    {
        /** @var BeforeAppendEvent $event */
        $event = $this->dispatcher->dispatch(new BeforeAppendEvent($streamName, $events));
        $this->eventStore->append($event->getStreamName(), $event->getEvents());
        $this->dispatcher->dispatch(new AfterAppendEvent($event->getStreamName(), $event->getEvents()));
    }

    /**
     * @return EventStore
     */
    public function getInnerEventStore(): EventStore
    {
        return $this->eventStore;
    }
}
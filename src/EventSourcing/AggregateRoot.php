<?php

namespace Historian\EventSourcing;

use Historian\Event\Event;
use Historian\Event\EventStream;
use Historian\Event\InMemoryEventStream;

/**
 * Class AggregateRoot
 *
 * An Aggregate Root is a state machine.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
abstract class AggregateRoot
{
    /**
     * The id of the aggregate
     * @var string
     */
    protected $id;
    /**
     * The state of the aggregate
     * @var array
     */
    protected $state;
    /**
     * The version of the aggregate
     * @var int
     */
    protected $version;
    /**
     * The UNIX timestamp of the last time it was modified
     * @var int
     */
    protected $lastModified;
    /**
     * The UNIX timestamp of the the time the aggregate was created
     * @var int
     */
    protected $created;
    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * @param EventStream $events
     * @param AggregateRoot|null $aggregate
     * @return AggregateRoot
     */
    private static function reconstituteFromHistory(EventStream $events, AggregateRoot $aggregate = null): AggregateRoot
    {
        if ($aggregate instanceof self) {
            $events->start($aggregate->version + 1);
        }

        foreach ($events as $event) {
            if ($aggregate === null) {
                $className = $event->get('_aggregateClass');
                $aggregate = new $className($event->get('_aggregateId'));
                $aggregate->created = $event->occurredAt();
                $aggregate->lastModified = $event->occurredAt();
            }
            $aggregate->apply($event);
        }
        return $aggregate;
    }

    /**
     * AggregateRoot constructor.
     * @noinspection PhpDocMissingThrowsInspection
     * @param string $aggregateId
     */
    public function __construct(string $aggregateId)
    {
        $this->id = $aggregateId;
        $this->state = [];
        $this->version = 0;
        $this->created = time();
        $this->lastModified = time();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function version(): int
    {
        return $this->version;
    }

    public function lastModified(): int
    {
        return $this->lastModified;
    }

    public function created(): int
    {
        return $this->created;
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @param Event $event
     */
    protected function publish(Event $event): void
    {
        $event = $event
            ->with('_version', $this->version)
            ->with('_aggregateClass', get_class($this))
            ->with('_aggregateId', $this->id);
        $this->events[] = $event;
        $this->apply($event);
    }

    /**
     * @param Event $event
     */
    protected function apply(Event $event): void
    {
        $this->version++;
        $this->lastModified = $event->occurredAt();
    }

    /**
     * @return EventStream
     */
    protected function popStoredEvents(): EventStream
    {
        $events = $this->events;
        $this->events = [];
        return new InMemoryEventStream(...$events);
    }
}
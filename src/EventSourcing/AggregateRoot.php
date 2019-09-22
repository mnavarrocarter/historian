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
     * @var string
     */
    protected $id;
    /**
     * @var array
     */
    protected $state;
    /**
     * @var EventStream|Event[]
     */
    private $events;

    /**
     * @param EventStream $events
     * @return AggregateRoot
     */
    protected static function reconstituteFromHistory(EventStream $events): AggregateRoot
    {
        /** @var AggregateRoot|null $class */
        $class = null;
        foreach ($events as $event) {
            if ($class === null) {
                $className = $event->get('_aggregateClass');
                $class = new $className($event->get('_aggregateId'));
                $class->state['_createdAt'] = $event->occurredAt();
            }
            $class->apply($event);
            $class->state['_version'] = $event->get('_version');
            $class->state['_lastModified'] = $event->occurredAt();
        }
        return $class;
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
        $this->state['_version'] = 0;
        $this->state['_createdAt'] = time();
        $this->state['_lastModified'] = time();
        $this->events = new InMemoryEventStream();
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @param Event $event
     */
    protected function publish(Event $event): void
    {
        $event = $event
            ->with('_version', $this->state['_version'])
            ->with('_aggregateClass', get_class($this))
            ->with('_aggregateId', $this->id);
        $this->events->push($event);
        $this->apply($event);
        $this->state['_lastModified'] = time();
        $this->state['_version']++;
    }

    /**
     * @param Event $event
     */
    abstract protected function apply(Event $event): void;
}
<?php

namespace Historian\EventSourcing;

use DateTimeImmutable;
use DateTimeInterface;
use Historian\EventStore\Event;
use Historian\EventStore\EventStream;
use Historian\EventStore\InMemoryEventStream;

/**
 * Class AggregateRoot
 *
 * An Aggregate Root is a state machine that has
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
     * @var DateTimeInterface
     */
    protected $createdAt;
    /**
     * @var DateTimeInterface
     */
    private $lastModified;
    /**
     * @var int
     */
    protected $version;
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
                $class->createdAt = $event->occurredAt();
            }
            $class->apply($event);
            $class->version = $event->get('_version');
            $class->lastModified = $event->occurredAt();
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
        $this->createdAt = new DateTimeImmutable();
        $this->lastModified = new DateTimeImmutable();
        $this->events = new InMemoryEventStream();
        $this->version = 0;
        $this->state = [];
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @param Event $event
     */
    protected function publish(Event $event): void
    {
        $this->version++;
        $event = $event
            ->with('_version', $this->version)
            ->with('_aggregateClass', get_class($this))
            ->with('_aggregateId', $this->id);
        $this->events->push($event);
        $this->apply($event);
        $this->lastModified = new DateTimeImmutable();
    }

    /**
     * @param Event $event
     */
    abstract protected function apply(Event $event): void;

    /**
     * @param array $exclude
     * @return array
     */
    public function toArray(array $exclude = []): array
    {
        $array['_id'] = $this->id;
        $array['_version'] = $this->version;
        $array['_createdAt'] = $this->createdAt->format(DATE_ATOM);
        $array['_lastModified'] = $this->lastModified->format(DATE_ATOM);
        foreach ($this->state as $key => $value) {
            if (!in_array($key, $exclude, true)) {
                $array[$key] = $value;
            }
        }
        return $array;
    }
}
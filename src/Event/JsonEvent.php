<?php
declare(strict_types=1);

namespace Historian\Event;

use JsonSerializable;

/**
 * Decorates an event to make it json-serializable.
 *
 * @package Historian\Event
 */
class JsonEvent implements JsonSerializable
{
    /**
     * @var Event
     */
    protected $event;

    /**
     * JsonEvent constructor.
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->event->eventId(),
            'name' => $this->event->eventName(),
            'payload' => $this->event->payload(),
            'occurredAt' => $this->event->occurredAt()
        ];
    }
}
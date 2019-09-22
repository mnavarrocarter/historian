<?php
declare(strict_types=1);

namespace Historian\Event;

use JsonSerializable;

/**
 * Class JsonEvent
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
            'payload' => $this->event->payload(),
            'occurredAt' => $this->event->occurredAt()
        ];
    }
}
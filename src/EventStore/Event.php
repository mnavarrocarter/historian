<?php

namespace Historian\EventStore;

use function Historian\uuid4;

/**
 * Class Event
 *
 * This class models a Domain Event.
 *
 * Events contain a payload, and always have an Uuid4 and a creation time.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class Event
{
    /**
     * The id of the event
     * @var string
     */
    protected $eventId;
    /**
     * The payload of the event
     * @var array
     */
    protected $payload;
    /**
     * The UNIX Timestamp of when the event happened
     * @var int
     */
    protected $occurredAt;

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @param string $eventName
     * @return Event
     */
    public static function withName(string $eventName): Event
    {
        $event = new self(uuid4(), time());
        $event->payload['_eventName'] = $eventName;
        return $event;
    }

    /**
     * Event constructor.
     * @param string $eventId
     * @param int $occurredAt
     * @param array $payload
     */
    protected function __construct(string $eventId, int $occurredAt, array $payload = [])
    {
        $this->eventId = $eventId;
        $this->occurredAt = $occurredAt;
        $this->payload = $payload;
    }

    /**
     * @param string $key
     * @param $value
     * @return Event
     */
    public function with(string $key, $value): Event
    {
        $cloned = clone $this;
        if (!array_key_exists($key, $cloned->payload)) {
            $cloned->payload[$key] = $value;
        }
        return $cloned;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->payload);
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        return $this->payload[$key] ?? $default;
    }

    /**
     * @return string
     */
    public function eventId(): string
    {
        return $this->eventId;
    }

    /**
     * @return int
     */
    public function occurredAt(): int
    {
        return $this->occurredAt;
    }
}
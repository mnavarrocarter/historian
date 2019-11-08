<?php

namespace Historian\Event;

use function Historian\uuid4;

/**
 * Class Event
 *
 * This class models a Domain Event.
 *
 * Events contain a payload, and always have a name, a unique id and a creation time.
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
     * The name of the event
     * @var string
     */
    protected $eventName;
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
    public static function named(string $eventName): Event
    {
        return new self(uuid4(), $eventName, time());
    }

    /**
     * Event constructor.
     * @param string $eventId
     * @param string $eventName
     * @param int $occurredAt
     * @param array $payload
     */
    public function __construct(string $eventId, string $eventName, int $occurredAt, array $payload = [])
    {
        $this->eventId = $eventId;
        $this->eventName = $eventName;
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

    public function eventName(): string
    {
        return $this->eventName;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @return int
     */
    public function occurredAt(): int
    {
        return $this->occurredAt;
    }
}
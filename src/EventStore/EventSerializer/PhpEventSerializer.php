<?php
declare(strict_types=1);

namespace Historian\EventStore\EventSerializer;

use Historian\Event\Event;

/**
 * Class PhpEventSerializer
 *
 * Serializes events using PHP's native serialization function and format
 *
 * @package Historian\EventStore\EventSerializer
 */
final class PhpEventSerializer implements EventSerializer
{
    public function serialize(Event $event): string
    {
        return serialize($event);
    }

    public function deserialize(string $serialized): Event
    {
        return unserialize($serialized, [Event::class]);
    }
}
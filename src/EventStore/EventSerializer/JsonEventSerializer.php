<?php

namespace Historian\EventStore\EventSerializer;

use Historian\Event\Event;
use Historian\Event\JsonEvent;

/**
 * Class JsonEventSerializer
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
final class JsonEventSerializer implements EventSerializer
{
    /**
     * @param Event $event
     * @return string
     */
    public function serialize(Event $event): string
    {
        return json_encode(new JsonEvent($event));
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @param string $serialized
     * @return Event
     */
    public function deserialize(string $serialized): Event
    {
        $data = json_decode($serialized, true);
        return new Event($data['id'], $data['name'], $data['occurredAt'], $data['payload']);
    }
}
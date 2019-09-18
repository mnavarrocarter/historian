<?php

namespace Historian\Serializer;

use Historian\EventStore\Event;

/**
 * Class JsonEventSerializer
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class JsonEventSerializer implements EventSerializer
{
    /**
     * @param Event $event
     * @return string
     */
    public function serialize(Event $event): string
    {
        return json_encode($event->toArray());
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @param string $serialized
     * @return Event
     */
    public function deserialize(string $serialized): Event
    {
        return Event::fromArray(json_decode($serialized, true));
    }
}
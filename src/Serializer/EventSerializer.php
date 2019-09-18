<?php

namespace Historian\Serializer;

use Historian\EventStore\Event;

/**
 * Interface EventSerializer
 *
 * Description of what this interface is for goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface EventSerializer
{
    /**
     * @param Event $event
     * @return string
     */
    public function serialize(Event $event): string;

    /**
     * @param string $serialized
     * @return Event
     */
    public function deserialize(string $serialized): Event;
}
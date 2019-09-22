<?php

namespace Historian\EventStore\StreamExtractor;

use Historian\Event\Event;

/**
 * Class EventNameStreamExtractor
 *
 * Takes the name of an event and creates an stream with it.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
final class EventNameStreamExtractor implements StreamExtractor
{
    public function extractFrom(Event $event): array
    {
        $streams = [];
        if ($event->has('_eventName')) {
            $streams[] = $event->get('_eventName');
        }
        return $streams;
    }
}
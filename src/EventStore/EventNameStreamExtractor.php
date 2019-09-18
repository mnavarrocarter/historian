<?php

namespace Historian\EventStore;

/**
 * Class EventNameStreamExtractor
 *
 * Takes the name of an event and creates an stream with it.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class EventNameStreamExtractor implements StreamExtractor
{
    public function getStreams(Event $event): array
    {
        $streams = [];
        if ($event->has('_eventName')) {
            $streams[] = $event->get('_eventName');
        }
        return $streams;
    }
}
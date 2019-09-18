<?php

namespace Historian\EventStore;

/**
 * Class AggregateClassStreamExtractor
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class AggregateClassStreamExtractor implements StreamExtractor
{
    public function getStreams(Event $event): array
    {
        $streams = [];
        if ($event->has('_aggregateClass')) {
            $streams[] = $event->get('_aggregateClass');
        }
        return $streams;
    }
}
<?php

namespace Historian\EventStore\StreamExtractor;

use Historian\Event\Event;

/**
 * Class AggregateClassStreamExtractor
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
final class AggregateClassStreamExtractor implements StreamExtractor
{
    public function extractFrom(Event $event): array
    {
        $streams = [];
        if ($event->has('_aggregateClass')) {
            $streams[] = $event->get('_aggregateClass');
        }
        return $streams;
    }
}
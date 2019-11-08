<?php

namespace Historian\EventStore\StreamExtractor;

use Historian\Event\Event;

/**
 * Interface StreamExtractor
 *
 * The base contract for an StreamExtractor.
 *
 * The idea behind a extractor is that, before persistence, it receives the event and has the chance
 * to return an array of stream names out of it. The event will be appended to those streams
 * when persisted.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface StreamExtractor
{
    /**
     * Returns the streams to which this event should be appended to.
     *
     * If no extra streams are necessary, it MUST return an empty array.
     *
     * @param Event $event
     * @return array
     */
    public function extractFrom(Event $event): array;
}
<?php

namespace Historian\EventStore;

/**
 * Interface StreamExtractor
 *
 * The base contract for an StreamExtractor.
 *
 * The idea behind a extractor is that, before persistence,
 * it receives the event and has the chance to return an
 * array of stream names out of it. The event will be appended
 * to those streams.
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
    public function getStreams(Event $event): array;
}
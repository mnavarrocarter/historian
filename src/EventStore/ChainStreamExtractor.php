<?php

namespace Historian\EventStore;

/**
 * Class ChainStreamExtractor
 *
 * This extractor chains one or more StreamExtractors together.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class ChainStreamExtractor implements StreamExtractor
{
    /**
     * @var StreamExtractor[]
     */
    private $extractors;

    /**
     * ChainStreamExtractor constructor.
     * @param StreamExtractor ...$extractors
     */
    public function __construct(StreamExtractor ...$extractors)
    {
        $this->extractors = $extractors;
    }

    /**
     * @param Event $event
     * @return array
     */
    public function getStreams(Event $event): array
    {
        $streams = [];
        foreach ($this->extractors as $extractor) {
            $streams[] = $extractor->getStreams($event);
        }
        return array_merge(...$streams);
    }
}
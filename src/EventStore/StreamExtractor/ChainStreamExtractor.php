<?php

namespace Historian\EventStore\StreamExtractor;
;

use Historian\Event\Event;

/**
 * Class ChainStreamExtractor
 *
 * This extractor chains one or more StreamExtractors together.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
final class ChainStreamExtractor implements StreamExtractor
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
     * @param StreamExtractor $extractor
     */
    public function push(StreamExtractor $extractor): void
    {
        $this->extractors[] = $extractor;
    }

    /**
     * @param Event $event
     * @return array
     */
    public function extractFrom(Event $event): array
    {
        $streams = [];
        foreach ($this->extractors as $extractor) {
            $streams[] = $extractor->extractFrom($event);
        }
        return array_merge(...$streams);
    }
}
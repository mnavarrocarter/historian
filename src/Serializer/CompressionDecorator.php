<?php

namespace Historian\Serializer;

use Historian\EventStore\Event;

/**
 * Class CompressionDecorator
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class CompressionDecorator implements EventSerializerDecorator
{
    /**
     * @var EventSerializer
     */
    private $eventSerializer;
    /**
     * @var int
     */
    private $compressionLevel;

    /**
     * CompressionDecorator constructor.
     * @param EventSerializer $eventSerializer
     * @param int $compressionLevel
     */
    public function __construct(EventSerializer $eventSerializer, int $compressionLevel = 3)
    {
        $this->eventSerializer = $eventSerializer;
        $this->compressionLevel = $compressionLevel;
    }

    /**
     * @return EventSerializer
     */
    public function getInnerEventSerializer(): EventSerializer
    {
        return $this->eventSerializer;
    }

    /**
     * @param Event $event
     * @return string
     */
    public function serialize(Event $event): string
    {
        $string = $this->eventSerializer->serialize($event);
        return gzcompress($string, $this->compressionLevel);
    }

    /**
     * @param string $serialized
     * @return Event
     */
    public function deserialize(string $serialized): Event
    {
        $decompressed = gzuncompress($serialized);
        return $this->eventSerializer->deserialize($decompressed);
    }
}
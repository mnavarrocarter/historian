<?php

namespace Historian\Tests\Serializer;

use Exception;
use Historian\EventSourcing\Event;
use Historian\Serializer\CompressionDecorator;
use Historian\Serializer\JsonEventSerializer;
use PHPUnit\Framework\TestCase;

class JsonEventSerializerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCompleteSerialization(): void
    {
        $serializer = new CompressionDecorator(new JsonEventSerializer());
        $event = Event::create('dummy-event', uuidV4())
            ->add('user', 'some-user-id')
            ->add('name', 'Some Name');

        $string = $serializer->serialize($event);

        $unserializedEvent = $serializer->deserialize($string);

        $this->assertEquals('some-user-id', $event->get('user'));
        $this->assertEquals('Some Name', $event->get('name'));
    }
}

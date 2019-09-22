<?php
declare(strict_types=1);

namespace Historian\Tests\EventStore\StorageDriver;

use Historian\EventStore\StorageDriver\FilesystemStorageDriver;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use function Historian\uuid4;

/**
 * Class FilesystemStorageDriverTest
 * @package Historian\Tests\EventStore\StorageDriver
 */
class FilesystemStorageDriverTest extends TestCase
{
    public function testEventIsPushedIntoStream(): void
    {
        // We create the virtual filesystem
        $fs = vfsStream::setup('root', 0755, []);

        $storage = new FilesystemStorageDriver($fs->url());

        $uuid = uuid4();
        $stream = 'master';

        $storage->pushEventToStream($uuid, $stream);
        $this->assertFileExists('vfs://root/4f/26aeafdb2367620a393c973eddbe8f8b846ebd');
    }

    public function testEventDataIsSaved(): void
    {
        // We create the virtual filesystem
        $fs = vfsStream::setup('root', 0755, []);

        $storage = new FilesystemStorageDriver($fs->url());
        $eventId = 'd439c5a6-5ab2-4d56-9e26-a8ece1d20470';

        $storage->saveEventData($eventId, 'event-data');
        $this->assertFileExists('vfs://root/5a/b4637491f1d3aefb4fb22212c5f5dcf24937c1');
        $this->assertEquals('event-data', file_get_contents('vfs://root/5a/b4637491f1d3aefb4fb22212c5f5dcf24937c1'));
    }

    public function testGetEventData(): void
    {
        // We create the virtual filesystem
        $fs = vfsStream::setup('root', 0755, [
            '5a/b4637491f1d3aefb4fb22212c5f5dcf24937c1' => 'event-data'
        ]);

        $storage = new FilesystemStorageDriver($fs->url());
        $eventId = 'd439c5a6-5ab2-4d56-9e26-a8ece1d20470';

        $data = $storage->getEventData($eventId);
        $this->assertEquals('event-data', $data);
    }

    public function testStreamDeletion(): void
    {
        // We create the virtual filesystem
        $fs = vfsStream::setup('root', 0755, []);

        $storage = new FilesystemStorageDriver($fs->url());

        $uuid = uuid4();
        $stream = 'master';

        $storage->pushEventToStream($uuid, $stream);
        $this->assertFileExists('vfs://root/4f/26aeafdb2367620a393c973eddbe8f8b846ebd');
        $this->assertTrue($storage->streamExists('master'));

        $storage->deleteStream($stream);
        $this->assertFileNotExists('vfs://root/4f/26aeafdb2367620a393c973eddbe8f8b846ebd');
        $this->assertFalse($storage->streamExists('master'));
    }

    public function testEventsInStream(): void
    {
        // We create the virtual filesystem
        $fs = vfsStream::setup('root', 0755, []);

        $storage = new FilesystemStorageDriver($fs->url());

        $id1 = '829f253f-7839-43e6-bb64-f367f636065b';
        $id2 = '3be37dff-2206-4cc4-b64a-29fc7bb198cc';
        $id3 = '3d955c07-9892-4ee0-844a-1a4ee1f13a3d';

        $storage->pushEventToStream($id1, 'master');
        $storage->pushEventToStream($id2, 'master');
        $storage->pushEventToStream($id3, 'master');

        $ids = iterator_to_array($storage->getEventsFromStream('master'));

        $this->assertCount(3, $ids);
        $this->assertEquals($ids[0], $id1);
        $this->assertEquals($ids[1], $id2);
        $this->assertEquals($ids[2], $id3);
    }

    public function testCountEventsInStream(): void
    {
        // We create the virtual filesystem
        $fs = vfsStream::setup('root', 0755, []);

        $storage = new FilesystemStorageDriver($fs->url());

        $id1 = '829f253f-7839-43e6-bb64-f367f636065b';
        $id2 = '3be37dff-2206-4cc4-b64a-29fc7bb198cc';
        $id3 = '3d955c07-9892-4ee0-844a-1a4ee1f13a3d';

        $storage->pushEventToStream($id1, 'master');
        $storage->pushEventToStream($id2, 'master');
        $storage->pushEventToStream($id3, 'master');

        $this->assertEquals(3, $storage->countEventsInStream('master'));
    }
}

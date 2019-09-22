<?php

namespace Historian\EventStore\Predis;

use Historian\EventStore\StorageDriver\StorageDriver;
use Iterator;
use Predis\Client;

/**
 * Class PredisStorageDriver
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
final class PredisStorageDriver implements StorageDriver
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $appName;

    /**
     * PredisStorageDriver constructor.
     * @param Client $client
     * @param string $appName
     */
    public function __construct(Client $client, string $appName = 'App')
    {
        $this->client = $client;
        $this->appName = trim($appName, ':');
    }

    /**
     * Checks if a list exists.
     *
     * @param string $streamName
     * @return bool
     */
    public function streamExists(string $streamName): bool
    {
        return (bool)$this->client->exists($this->streamListKey($streamName));
    }

    /**
     * Pushes an event identifier to a list.
     *
     * @param string $eventId
     * @param string $streamName
     */
    public function pushEventToStream(string $eventId, string $streamName): void
    {
        $this->client->rpush($this->streamListKey($streamName), [$eventId]);
    }

    /**
     * @param string $eventId
     * @param string $event
     */
    public function saveEventData(string $eventId, string $event): void
    {
        $this->client->hset($this->eventDataKey($eventId), $eventId, $event);
    }

    /**
     * @param string $streamName
     * @return int
     */
    public function countEventsInStream(string $streamName): int
    {
        return $this->client->scard($this->streamListKey($streamName));
    }

    /**
     * @param string $eventId
     * @return string
     */
    public function getEventData(string $eventId): string
    {
        return $this->client->hget($this->eventDataKey($eventId), $eventId);
    }

    /**
     * @param string $streamName
     * @param int $start
     * @param int|null $size
     * @return Iterator
     */
    public function getEventsFromStream(string $streamName, int $start = 0, int $size = null): Iterator
    {
        // We query the elements of the stream with pagination settings.
        $stop = $size === null ? -1 : ($start + $size)-1;
        return yield from $this->client->lrange($this->streamListKey($streamName), $start, $stop);
    }

    /**
     * Deletes a stream.
     *
     * @param string $streamName
     */
    public function deleteStream(string $streamName): void
    {
        $this->client->del([$this->streamListKey($streamName)]);
    }

    /**
     * @param string $listName
     * @return string
     */
    protected function streamListKey(string $listName): string
    {
        return sprintf(
            '%s:%s:%s',
            $this->appName,
            'Streams',
            substr(sha1($listName), 0, 8)
        );
    }

    /**
     * @param string $eventId
     * @return string
     */
    protected function eventDataKey(string $eventId): string
    {
        return sprintf(
            '%s:%s:%s',
            $this->appName,
            'Events',
            substr(sha1($eventId), 0, 4)
        );
    }
}
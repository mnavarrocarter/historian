<?php

namespace Historian\EventStore\Predis;

use Predis\Client;

/**
 * Class PredisClientWrapper
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class PredisClientWrapper
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
     * PredisClientWrapper constructor.
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
    public function eventStreamExists(string $streamName): bool
    {
        return (bool)$this->client->exists($this->streamListKey($streamName));
    }

    /**
     * Pushes an event identifier to a list.
     *
     * @param string $listName
     * @param string $eventId
     */
    public function pushEventIdToList(string $listName, string $eventId): void
    {
        $this->client->rpush($this->streamListKey($listName), [$eventId]);
    }

    /**
     * @param string $eventId
     * @param string $event
     */
    public function saveEventDataHash(string $eventId, string $event): void
    {
        $this->client->hset($this->eventDataKey($eventId), $eventId, $event);
    }

    /**
     * @param string $eventId
     * @return string
     */
    public function readEventData(string $eventId): string
    {
        return $this->client->hget($this->eventDataKey($eventId), $eventId);
    }

    /**
     * @param string $streamName
     * @param int $start
     * @param int $stop
     * @return array
     */
    public function readStreamEventsIds(string $streamName, int $start = 0, int $stop = -1): array
    {
        return $this->client->lrange($this->streamListKey($streamName), $start, $stop);
    }

    /**
     * Deletes a list, which represents a stream. The event data is not
     * deleted though.
     *
     * @param string $streamName
     */
    public function deleteStreamList(string $streamName): void
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

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
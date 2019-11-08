<?php

namespace Historian\Projector\Predis;

use Historian\Projector\ProjectorTracker;
use Predis\Client;

/**
 * Class PredisProjectorTracker
 *
 * Tracks the offset of the last event handled and saves it in Redis.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class PredisProjectorTracker implements ProjectorTracker
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $baseName;

    /**
     * RedisPersistentState constructor.
     * @param Client $client
     * @param string $baseName
     */
    public function __construct(Client $client, string $baseName = 'Projections')
    {
        $this->client = $client;
        $this->baseName = trim($baseName, ':');
    }

    /**
     * @param string $id
     * @param int $eventId
     */
    public function track(string $id, int $eventId): void
    {
        $this->client->set($this->key($id), $eventId);
    }

    /**
     * @param string $id
     */
    public function reset(string $id): void
    {
        $this->client->set($this->key($id), 0);
    }

    /**
     * @inheritDoc
     */
    public function lastTrackedEvent(string $id): int
    {
        $eventId = $this->client->get($this->key($id));
        if ($eventId === '') {
            return -1;
        }
        return (int) $eventId;
    }

    private function key(string $id): string
    {
        return sprintf('%s:%s', $this->baseName, sha1($id));
    }
}
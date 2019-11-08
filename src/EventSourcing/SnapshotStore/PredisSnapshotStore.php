<?php
declare(strict_types=1);

namespace Historian\EventSourcing\SnapshotStore;

use Historian\EventSourcing\AggregateRoot;
use Predis\Client;

/**
 * Class PredisSnapshotStore
 * @package Historian\EventSourcing\SnapshotStore
 */
class PredisSnapshotStore implements SnapshotStore
{
    /**
     * @var Client
     */
    private $predis;
    /**
     * @var string
     */
    private $prefix;

    /**
     * PredisSnapshotStore constructor.
     * @param Client $predis
     * @param string $prefix
     */
    public function __construct(Client $predis, string $prefix = 'Snapshots')
    {
        $this->predis = $predis;
        $this->prefix = $prefix;
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    public function save(AggregateRoot $aggregateRoot): void
    {
        $this->predis->set($this->keyName($aggregateRoot->id()), serialize($aggregateRoot));
    }

    /**
     * @param string $aggregateId
     * @return bool
     */
    public function has(string $aggregateId): bool
    {
        return (bool) $this->predis->exists($this->keyName($aggregateId));
    }

    /**
     * @param string $aggregateId
     * @return AggregateRoot
     */
    public function get(string $aggregateId): AggregateRoot
    {
        return unserialize($this->predis->get($aggregateId), [AggregateRoot::class]);
    }

    /**
     * @param string $id
     * @return string
     */
    protected function keyName(string $id): string
    {
        $hash = sha1($id);
        return sprintf(sprintf(
            '%s:%s:%s',
            $this->prefix,
            substr($hash, 0, 2),
            substr($hash, 2)
        ));
    }
}
<?php
declare(strict_types=1);

namespace Historian\EventStore\StorageDriver;

use Iterator;

/**
 * Interface StorageDriver
 * @package Historian\EventStore\StorageDriver
 */
interface StorageDriver
{
    /**
     * @param string $eventId
     * @param string $streamName
     */
    public function pushEventToStream(string $eventId, string $streamName): void;

    /**
     * @param string $eventId
     * @param string $data
     */
    public function saveEventData(string $eventId, string $data): void;

    /**
     * @param string $streamName
     */
    public function deleteStream(string $streamName): void;

    /**
     * Checks whether an event stream exists.
     *
     * @param string $streamName
     * @return bool
     */
    public function streamExists(string $streamName): bool;

    /**
     * Returns a list of event ids from an Event Stream
     *
     * @param string $streamName
     * @param int $start
     * @param int|null $size If null, returns to the end of the list.
     * @return Iterator
     */
    public function getEventsFromStream(string $streamName, int $start = 0, int $size = null): Iterator;

    /**
     * @param string $eventId
     * @return string
     */
    public function getEventData(string $eventId): string;

    /**
     * @param string $streamName
     * @return int
     */
    public function countEventsInStream(string $streamName): int;
}
<?php
declare(strict_types=1);

namespace Historian\EventStore\StorageDriver;

use Iterator;

/**
 * Class FilesystemStorageDriver
 * @package Historian\EventStore\StorageDriver
 */
final class FilesystemStorageDriver implements StorageDriver
{
    protected const BYTES_PER_LINE = 37;

    /**
     * @var string
     */
    private $path;

    /**
     * FilesystemStorageDriver constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param string $eventId
     * @param string $streamName
     */
    public function pushEventToStream(string $eventId, string $streamName): void
    {
        $filename = $this->file($streamName);
        $this->ensurePath(pathinfo($filename, PATHINFO_DIRNAME));
        file_put_contents($filename, $eventId.PHP_EOL, FILE_APPEND);
    }

    public function saveEventData(string $eventId, string $data): void
    {
        $filename = $this->file($eventId);
        $this->ensurePath(pathinfo($filename, PATHINFO_DIRNAME));
        file_put_contents($filename, $data);
    }

    public function deleteStream(string $streamName): void
    {
        unlink($this->file($streamName));
    }

    public function streamExists(string $streamName): bool
    {
        return is_file($this->file($streamName));
    }

    /**
     * @param string $streamName
     * @param int $start
     * @param int|null $size
     * @return Iterator
     */
    public function getEventsFromStream(string $streamName, int $start = 0, int $size = null): Iterator
    {
        $file = $this->file($streamName);
        $stream = fopen($file, 'rb');
        $offset = $start * self::BYTES_PER_LINE;

        // We set the bytes to where we are going to read.
        $max = $size === null ? filesize($file) : $size * self::BYTES_PER_LINE;

        fseek($stream, $offset);

        while (true) {
            $data = trim(fread($stream, self::BYTES_PER_LINE));
            if ($data === '') {
                break;
            }
            yield $data;
            if (ftell($stream) >= $max) {
                break;
            }
        }
        fclose($stream);
    }

    public function getEventData(string $eventId): string
    {
        return file_get_contents($this->file($eventId));
    }

    /**
     * @param string $streamName
     * @return int
     */
    public function countEventsInStream(string $streamName): int
    {
        $path = $this->file($streamName);
        $bytes = filesize($path);
        if (!$bytes) {
            return 0;
        }
        return $bytes / self::BYTES_PER_LINE;
    }

    /**
     * @param string $path
     */
    protected function ensurePath(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0755, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Could not create "%s" directory', $path));
        }
    }

    /**
     * @param string $identifier
     * @return string
     */
    protected function file(string $identifier): string
    {
        $hash = sha1($identifier);
        $dir = substr($hash, 0, 2);
        $file = substr($hash, 2);
        return sprintf('%s/%s/%s', $this->path, $dir, $file);
    }
}
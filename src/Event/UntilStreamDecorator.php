<?php
declare(strict_types=1);

namespace Historian\Event;

use DateTimeInterface;
use Iterator;

/**
 * Class UntilStreamDecorator
 *
 * Decorates an Event Stream to stop iteration when a certain date is reached.
 *
 * @package Historian\Event
 */
final class UntilStreamDecorator implements EventStream
{
    /**
     * @var EventStream
     */
    private $stream;
    /**
     * @var DateTimeInterface
     */
    private $until;

    /**
     * UntilStreamDecorator constructor.
     * @param EventStream $stream
     * @param DateTimeInterface $until
     */
    public function __construct(EventStream $stream, DateTimeInterface $until)
    {
        $this->stream = $stream;
        $this->until = $until;
    }

    public function getIterator(): Iterator
    {
        $timestamp = $this->until->getTimestamp();
        foreach ($this->stream as $event) {
            if ($event->occurredAt() >= $timestamp) {
                break;
            }
            yield $event;
        }
    }

    public function start(int $start): void
    {
        $this->stream->start($start);
    }

    public function size(int $size): void
    {
        $this->stream->size($size);
    }

    public function count(): int
    {
        return $this->stream->count();
    }
}
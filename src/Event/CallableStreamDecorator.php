<?php
declare(strict_types=1);

namespace Historian\Event;

use Iterator;

/**
 * Class CallableStreamDecorator
 *
 * Applies a callable to a Event Stream on iteration.
 *
 * @package Historian\Event
 */
final class CallableStreamDecorator implements EventStream
{
    /**
     * @var EventStream
     */
    private $stream;
    /**
     * @var callable
     */
    private $callable;

    /**
     * CallableStreamDecorator constructor.
     * @param EventStream $stream
     * @param callable $callable
     */
    public function __construct(EventStream $stream, callable $callable)
    {
        $this->stream = $stream;
        $this->callable = $callable;
    }

    /**
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        foreach ($this->stream as $event) {
            yield ($this->callable)($event);
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
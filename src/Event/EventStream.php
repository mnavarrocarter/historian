<?php

namespace Historian\Event;

use Countable;
use Iterator;
use IteratorAggregate;

/**
 * Interface EventIterator
 *
 * The contract to implement an iterable collection of events.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface EventStream extends IteratorAggregate, Countable
{
    /**
     * @return Iterator|Event[]
     */
    public function getIterator(): Iterator;

    /**
     * Defines in which event the iteration starts.
     *
     * The first event MUST be 0.
     *
     * @param int $start
     */
    public function start(int $start): void;

    /**
     * Defines the maximum number of events to fetch.
     *
     * @param int $size
     */
    public function size(int $size): void;

    /**
     * @return int
     */
    public function count(): int;
}
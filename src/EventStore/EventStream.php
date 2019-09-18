<?php

namespace Historian\EventStore;

use DateTimeInterface;
use Iterator;
use IteratorAggregate;

/**
 * Interface EventIterator
 *
 * The contract to implement a collection of events.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface EventStream extends IteratorAggregate
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
     * Iterates over the events until a certain date is reached.
     *
     * @param DateTimeInterface $time
     */
    public function until(DateTimeInterface $time): void;

    /**
     * Reduces the Event Stream to a piece of state.
     *
     * @param array $state The initial state
     * @param callable $reducer The reducer function. Takes the state and the event as arguments.
     * @return array The resulting state
     */
    public function reduce(array $state, callable $reducer): array;

    /**
     * Applies a callable to the event stream to be run on iteration.
     *
     * @param callable $callable The callable to apply. It takes two arguments: the event and the key.
     */
    public function apply(callable $callable): void;
}
<?php
declare(strict_types=1);

namespace Historian\EventSourcing;

use JsonSerializable;

/**
 * Class JsonAggregateRoot
 *
 * Decorates an Aggregate Root to serialize it to Json
 *
 * @package Historian\EventSourcing
 */
final class JsonAggregateRoot implements JsonSerializable
{
    /**
     * @var AggregateRoot
     */
    private $aggregateRoot;

    /**
     * JsonAggregateRoot constructor.
     * @param AggregateRoot $aggregateRoot
     */
    public function __construct(AggregateRoot $aggregateRoot)
    {
        $this->aggregateRoot = $aggregateRoot;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $extract = \Closure::fromCallable(function(): array {
            return array_merge(['_id' => $this->id], $this->state);
        })->bindTo($this->aggregateRoot, AggregateRoot::class);
        return $extract();
    }
}
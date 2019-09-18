<?php

namespace Historian\EventSourcing;

/**
 * Class ReadOnlyRepository
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface AggregateRepository
{
    /**
     * @param string $aggregateId
     * @return AggregateRoot|null
     */
    public function findAggregate(string $aggregateId): ?AggregateRoot;

    /**
     * @param string $aggregateId
     * @return bool
     */
    public function aggregateExists(string $aggregateId): bool;

    /**
     * @param AggregateRoot $aggregateRoot
     */
    public function saveAggregate(AggregateRoot $aggregateRoot): void;

    /**
     * @param AggregateRoot $aggregateRoot
     */
    public function deleteAggregate(AggregateRoot $aggregateRoot): void;
}
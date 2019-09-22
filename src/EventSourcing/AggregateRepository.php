<?php

namespace Historian\EventSourcing;

/**
 * Class AggregateRepository
 *
 * The contract for an aggregate repository
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
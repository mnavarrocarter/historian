<?php

namespace Historian\EventSourcing;

/**
 * Interface DecoratedAggregateRepository
 *
 * Description of what this interface is for goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface DecoratedAggregateRepository extends AggregateRepository
{
    /**
     * @return AggregateRepository
     */
    public function getInnerRepository(): AggregateRepository;
}
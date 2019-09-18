<?php

namespace Historian\EventSourcing\EventDispatcher;

use Historian\EventSourcing\AggregateRepository;
use Historian\EventSourcing\AggregateRoot;
use Historian\EventSourcing\DecoratedAggregateRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * Class EventDispatcherAggregateRepository
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
final class EventDispatcherAggregateRepository implements DecoratedAggregateRepository
{
    /**
     * @var AggregateRepository
     */
    private $aggregateRepository;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * EventDispatcherAggregateRepository constructor.
     * @param AggregateRepository $aggregateRepository
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(AggregateRepository $aggregateRepository, EventDispatcherInterface $dispatcher)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $aggregateId
     * @return AggregateRoot|null
     */
    public function findAggregate(string $aggregateId): ?AggregateRoot
    {
        $aggregate = $this->aggregateRepository->findAggregate($aggregateId);
        if ($aggregate) {
            $this->dispatcher->dispatch(self::AGGREGATE_FETCHED, [$aggregate]);
        }
        return $aggregate;
    }

    /**
     * @param AggregateRoot $aggregateRoot
     * @throws Throwable
     */
    public function saveAggregate(AggregateRoot $aggregateRoot): void
    {
        try {
            $this->aggregateRepository->saveAggregate($aggregateRoot);
            $this->dispatcher->dispatch(self::AGGREGATE_SAVED, [$aggregateRoot]);
        } catch (Throwable $exception) {
            $this->dispatcher->dispatch(self::AGGREGATE_SAVE_FAILED, [$aggregateRoot, $exception]);
            throw $exception;
        }
    }

    public function aggregateExists(string $aggregateId): bool
    {
        return $this->aggregateRepository->aggregateExists($aggregateId);
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    public function deleteAggregate(AggregateRoot $aggregateRoot): void
    {
        $this->aggregateRepository->deleteAggregate($aggregateRoot);
        $this->dispatcher->dispatch(self::AGGREGATE_DELETED, [$aggregateRoot]);
    }

    /**
     * @return AggregateRepository
     */
    public function getInnerRepository(): AggregateRepository
    {
        return $this->aggregateRepository;
    }
}
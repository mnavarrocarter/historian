<?php

namespace Historian;

use Closure;
use Historian\EventSourcing\AggregateRepository;
use Historian\EventSourcing\EventDispatcher\EventDispatcherAggregateRepository;
use Historian\EventSourcing\EventSourcedAggregateRepository;
use Historian\EventStore\EventDispatcher\EventDispatcherEventStore;
use Historian\EventStore\EventStore;
use Historian\EventStore\Http\EventsHttpHandler;
use Historian\EventStore\Predis\PredisStorageDriver;
use Historian\Projector\ProjectorManager;
use Historian\Projector\ProjectorTracker;
use Historian\Projector\Predis\PredisProjectorTracker;
use Historian\Serializer\CompressionDecorator;
use Historian\Serializer\EventSerializer;
use Historian\Serializer\JsonEventSerializer;
use Historian\Util\ClosurePropertyAccessor;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Predis\Client as PredisClient;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Class EventSourcingServiceProvider
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class HistorianServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    private $options;

    /**
     * EventSourcingServiceProvider constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'event_store_dispatch_events' => false,
            'aggregate_repository_dispatch_events' => false,
            'event_dispatcher_service' => EventDispatcherInterface::class,
            'predis_service' => PredisClient::class,
            'predis_key_prefix' => 'historian',
            'response_factory_service' => ResponseFactoryInterface::class,
            'compress_events' => false,
        ], $options);
    }

    protected $provides = [
        EventsHttpHandler::class,
        EventStore::class,
        EventSerializer::class,
        AggregateRepository::class,
        PredisStorageDriver::class,
        ClosurePropertyAccessor::class,
        ProjectorManager::class,
        ProjectorTracker::class
    ];

    public function register(): void
    {
        // We register the predis wrapper
        $this->leagueContainer->share(PredisStorageDriver::class)
            ->addArgument($this->options['predis_service'])
            ->addArgument($this->options['predis_key_prefix']);

        // Event Store
        $this->leagueContainer->share(
            EventStore::class,
            Closure::fromCallable([$this, 'buildEventStore'])
        );

        // Event Serializer
        $this->leagueContainer->share(
            EventSerializer::class,
            Closure::fromCallable([$this, 'buildEventSerializer'])
        );

        // Aggregate Repository
        $this->leagueContainer->share(AggregateRepository::class, EventSourcedAggregateRepository::class)
            ->addArgument(EventStore::class)
            ->addArgument(ClosurePropertyAccessor::class);

        // Property Accessor
        $this->leagueContainer->share(ClosurePropertyAccessor::class);

        // Projector manager
        $this->leagueContainer->share(ProjectorManager::class)
            ->addArgument(EventStore::class)
            ->addArgument(ProjectorTracker::class);

        // Projector tracker
        $this->leagueContainer->share(ProjectorTracker::class, PredisProjectorTracker::class)
            ->addArgument(PredisClient::class);

        // List Events Handler
        $this->leagueContainer->share(EventsHttpHandler::class)
            ->addArgument(EventStore::class)
            ->addArgument($this->options['response_factory_service'])
            ->addArgument($this->options['list_events_handler']);
    }

    /**
     * @return AggregateRepository
     */
    protected function buildAggregateRepo(): AggregateRepository
    {
        $repository = new EventSourcedAggregateRepository(
            $this->leagueContainer->get(EventStore::class),
            $this->leagueContainer->get(ClosurePropertyAccessor::class)
        );

        if ($this->options['aggregate_repository_dispatch_events'] === true) {
            $repository = new EventDispatcherAggregateRepository(
                $repository,
                $this->leagueContainer->get($this->options['event_dispatcher_service'])
            );
        }

        return $repository;
    }

    /**
     * @return EventStore
     */
    protected function buildEventStore(): EventStore
    {
        $eventStore = new PredisEventStore(
            $this->leagueContainer->get(PredisStorageDriver::class),
            $this->leagueContainer->get(EventSerializer::class)
        );

        if ($this->options['event_store_dispatch_events'] === true) {
            $eventStore = new EventDispatcherEventStore(
                $eventStore,
                $this->leagueContainer->get($this->options['event_dispatcher_service'])
            );
        }

        return $eventStore;
    }

    /**
     * @return EventSerializer
     */
    protected function buildEventSerializer(): EventSerializer
    {
        $serializer = new JsonEventSerializer();
        if ($this->options['compress_events'] === true) {
            $serializer = new CompressionDecorator($serializer);
        }
        return $serializer;
    }
}
<?php

namespace Historian\Projector;

use ArrayIterator;
use Historian\EventStore\EventStore;
use InfiniteIterator;

/**
 * Class ProjectorManager
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class ProjectorManager
{
    /**
     * @var Projector[]
     */
    private $projectors;
    /**
     * @var ProjectorTracker
     */
    private $tracker;
    /**
     * @var EventStore
     */
    private $store;

    /**
     * ProjectorManager constructor.
     * @param EventStore $store
     * @param ProjectorTracker $tracker
     */
    public function __construct(EventStore $store, ProjectorTracker $tracker)
    {
        $this->store = $store;
        $this->tracker = $tracker;
        $this->projectors = [];
    }

    /**
     * @param string $id
     * @param Projector $projector
     */
    public function register(string $id, Projector $projector): void
    {
        $this->projectors[$id] = $projector;
    }

    public function run(): void
    {
        /** @var Projector[] $iterator */
        $iterator = new InfiniteIterator(new ArrayIterator($this->projectors));
        foreach ($iterator as $id => $projector) {
            sleep(1);
            $lastEventId = $this->tracker->lastTrackedEventId($id);
            $streamName = 'master';
            if ($projector instanceof StreamSpecificProjector) {
                $streamName = $projector->getStreamName();
            }
            $events = $this->store->load($streamName);
            $events->start($lastEventId + 1);

            foreach ($events as $event) {
                try {
                    $projector->project($event);
                    $lastEventId++;
                    $this->tracker->track($id, $lastEventId);
                } catch (ProjectionFailedException $exception) {
                    unset($this->projectors[$id]);
                    break;
                }
            }
        }
    }
}
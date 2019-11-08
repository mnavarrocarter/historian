<?php
declare(strict_types=1);

namespace Historian\Projector;

use Historian\EventStore\EventStore;
use RuntimeException;

/**
 * Class ShortProjectorRunner
 * @package Historian\Projector
 */
final class ShortProjectorRunner implements ProjectorRunner
{
    /**
     * @var EventStore
     */
    private $eventStore;
    /**
     * @var ProjectorTracker
     */
    private $tracker;
    /**
     * @var Projector[]
     */
    protected $projectors;

    /**
     * ShortProjectorRunner constructor.
     * @param EventStore $eventStore
     * @param ProjectorTracker $tracker
     */
    public function __construct(EventStore $eventStore, ProjectorTracker $tracker)
    {
        $this->eventStore = $eventStore;
        $this->tracker = $tracker;
        $this->projectors = [];
    }

    /**
     * @param Projector $projector
     */
    public function add(Projector $projector): void
    {
        $name = $projector->getName();
        foreach ($this->projectors as $storedProjector) {
            if ($name === $storedProjector->getName()) {
                throw new RuntimeException(sprintf('Projector named "%s" already exists in runner', $name));
            }
        }
        $this->projectors[] = $projector;
    }

    /**
     * Runs the projector.
     */
    public function run(): void
    {
        foreach ($this->projectors as $projector) {
            $projector->process($this->eventStore, $this->tracker);
        }
    }
}
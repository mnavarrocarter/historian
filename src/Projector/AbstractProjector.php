<?php
declare(strict_types=1);

namespace Historian\Projector;

use Historian\EventStore\EventStore;

/**
 * Class AbstractProjector
 * @package Historian\Projector
 */
abstract class AbstractProjector implements Projector
{
    /**
     * @return string
     */
    public function getStream(): string
    {
        return 'master';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return get_class($this);
    }

    /**
     * @inheritDoc
     */
    public function process(EventStore $eventStore, ProjectorTracker $tracker): void
    {
        $name = $this->getName();
        $stream = $eventStore->load($this->getStream());

        $lastTracked = $tracker->lastTrackedEvent($name);
        if ($lastTracked !== null) {
            $start = $lastTracked +1;
        } else {
            $start = 0;
        }

        $stream->start($start);

        foreach ($stream as $event) {
            $methodName = $this->toCamelCase($event->eventName());
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($event);
            }
            $start++;
            $tracker->track($name, $start);
        }
    }

    private function toCamelCase(string $string): string
    {
        return 'handle'.str_replace([' ', '_', '-'], '', ucwords($string, ' _-'));
    }
}
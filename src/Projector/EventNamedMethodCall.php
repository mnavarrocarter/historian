<?php

namespace Historian\Projector;

use Historian\Event\Event;
use RuntimeException;

/**
 * Trait EventNamedMethodCall
 *
 * Description of what this class does goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
trait EventNamedMethodCall
{
    /**
     * @param Event $event
     * @return void
     */
    public function project(Event $event): void
    {
        $methodName = $this->toCamelCase($event->get('_eventName'));
        if (!method_exists($this, $methodName)) {
            throw new RuntimeException(sprintf(
                'Class must have a method called "%s" to apply the "%s" event.',
                $methodName,
                $event->get('_eventName')
            ));
        }
        $this->{$methodName}($event);
    }

    private function toCamelCase(string $string): string
    {
        return 'project'.str_replace([' ', '_', '-'], '', ucwords($string, ' _-'));
    }
}
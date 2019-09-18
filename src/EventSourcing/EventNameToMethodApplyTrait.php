<?php

namespace Historian\EventSourcing;

use Historian\EventStore\Event;
use RuntimeException;

/**
 * Trait EventNameToMethodApplyTrait
 *
 * Overrides the apply function in the AggregateRoot to execute a method after the
 * camelized version of the event name
 *
 * Example: if event name is "user-was-updated", then a method called "applyUserWasUpdated" will
 * be executed.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
trait EventNameToMethodApplyTrait
{
    /**
     * @param Event $event
     * @return void
     */
    protected function apply(Event $event): void
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
        return 'apply'.str_replace([' ', '_', '-'], '', ucwords($string, ' _-'));
    }
}
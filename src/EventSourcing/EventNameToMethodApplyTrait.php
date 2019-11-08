<?php

namespace Historian\EventSourcing;

use Historian\Event\Event;
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
 *
 * @property int version
 * @property int lastModified
 */
trait EventNameToMethodApplyTrait
{
    /**
     * @param Event $event
     * @return void
     */
    protected function apply(Event $event): void
    {
        $methodName = $this->createMethodName($event->eventName());
        if (!method_exists($this, $methodName)) {
            throw new RuntimeException(sprintf(
                'Class must have a method called "%s" in order to apply the "%s" event to it.',
                $methodName,
                $event->get('_eventName')
            ));
        }
        $this->{$methodName}($event);
        $this->version++;
        $this->lastModified = time();
    }

    private function createMethodName(string $string): string
    {
        return 'apply'.str_replace([' ', '_', '-'], '', ucwords($string, ' _-'));
    }
}
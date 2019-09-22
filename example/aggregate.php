<?php

use Historian\Event\Event;
use Historian\EventSourcing\AggregateRoot;
use Historian\EventSourcing\EventNameToMethodApplyTrait;
use function Historian\uuid4;

require_once __DIR__.'/../vendor/autoload.php';

class Building extends AggregateRoot
{
    use EventNameToMethodApplyTrait;

    public static function create(): Building
    {
        $building = new self(uuid4());
        $building->publish(Event::named('building-created'));
        return $building;
    }

    /**
     * @param string $personId
     */
    public function registerPersonEntrance(string $personId): void
    {
        if ($this->hasPerson($personId)) {
            $this->publish(
                Event::named('entrance-anomaly-detected')
                    ->with('personId', $personId)
            );
            return;
        }
        $this->publish(
            Event::named('person-has-entered')
                ->with('personId', $personId)
        );
    }

    /**
     * @param string $personId
     */
    public function registerPersonExit(string $personId): void
    {
        if (!$this->hasPerson($personId)) {
            $this->publish(
                Event::named('exit-anomaly-detected')
                    ->with('personId', $personId)
            );
            return;
        }
        $this->publish(
            Event::named('person-has-exited')
                ->with('personId', $personId)
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    protected function applyPersonHasEntered(Event $event): void
    {
        $this->state['people'][] = $event->get('personId');
    }

    protected function applyPersonHasExited(Event $event): void
    {
        $this->state['people'] = array_filter($this->state['people'], static function (string $id) use ($event) {
            return $id !== $event->get('personId');
        });
    }

    protected function applyBuildingCreated(Event $event): void
    {
        $this->state['people'] = [];
    }

    protected function applyExitAnomalyDetected(Event $event): void
    {

    }

    protected function applyEntranceAnomalyDetected(Event $event): void
    {

    }

    public function peopleCount(): int
    {
        return count($this->state['people']);
    }

    public function getPeople(): array
    {
        return $this->state['people'];
    }

    protected function hasPerson(string $personId): bool
    {
        return in_array($personId, $this->state['people'], true);
    }
}
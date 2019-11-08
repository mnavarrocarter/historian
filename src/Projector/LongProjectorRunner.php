<?php
declare(strict_types=1);


namespace Historian\Projector;

/**
 * Class LongProjectorRunner
 * @package Historian\Projector
 */
class LongProjectorRunner implements ProjectorRunner
{
    /**
     * @var ProjectorRunner
     */
    private $runner;
    /**
     * @var int
     */
    private $sleepSeconds;

    /**
     * LongProjectorRunner constructor.
     * @param ProjectorRunner $runner
     * @param int $sleepSeconds
     */
    public function __construct(ProjectorRunner $runner, int $sleepSeconds = 1)
    {
        $this->runner = $runner;
        $this->sleepSeconds = $sleepSeconds;
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        while (true) {
            $this->runner->run();
            sleep($this->sleepSeconds);
        }
    }
}
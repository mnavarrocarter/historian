<?php
declare(strict_types=1);

namespace Historian\Projector;

/**
 * Interface ProjectorRunner
 * @package Historian\Projector
 */
interface ProjectorRunner
{
    /**
     * Runs the projector.
     */
    public function run(): void;
}
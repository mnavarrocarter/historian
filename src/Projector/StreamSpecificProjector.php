<?php

namespace Historian\Projector;

/**
 * Interface StreamSpecificProjector
 *
 * Marks a projector as stream specific.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface StreamSpecificProjector
{
    /**
     * @return string
     */
    public function getStreamName(): string;
}
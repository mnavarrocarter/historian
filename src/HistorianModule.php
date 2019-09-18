<?php

namespace Historian;

use Espresso\HttpModule\HttpModule;
use Historian\EventStore\Http\ListEventsHandler;

/**
 * Class HistorianModule
 *
 * The Http Module of Historian.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class HistorianModule
{
    /**
     * @param HttpModule $module
     */
    public function __invoke(HttpModule $module)
    {
        $module->get('/events', ListEventsHandler::class);
    }
}
<?php

namespace Historian\Serializer;

/**
 * Interface EventSerializerDecorator
 *
 * Description of what this interface is for goes here.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
interface EventSerializerDecorator extends EventSerializer
{
    /**
     * @return EventSerializer
     */
    public function getInnerEventSerializer(): EventSerializer;
}
<?php
declare(strict_types=1);

namespace Historian\HttpEventStore\ContentType;

use Historian\Event\EventStream;

/**
 * Interface ContentTypeMaker
 *
 * The contract for a content type maker.
 *
 * @package Historian\HttpEventStore
 */
interface ContentTypeMaker
{
    /**
     * @param string $contentType
     * @param EventStream $stream
     * @return ContentType
     */
    public function make(string $contentType, EventStream $stream): ContentType;
}
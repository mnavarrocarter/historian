<?php
declare(strict_types=1);

namespace Historian\HttpEventStore\ContentType;

/**
 * Class ContentType
 * @package Historian\HttpEventStore\ContentType
 */
class ContentType
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $data;

    /**
     * ContentType constructor.
     * @param string $name
     * @param string $data
     */
    public function __construct(string $name, string $data)
    {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }
}
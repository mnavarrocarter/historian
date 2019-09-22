<?php
declare(strict_types=1);

namespace Historian\Util;

/**
 * Interface PropertyAccessor
 *
 * @package Historian\Util
 */
interface PropertyAccessor
{
    /**
     * @param object $object
     * @param string $prop
     * @param string|null $scope
     * @return bool
     */
    public function has(object $object, string $prop, string $scope = null): bool;

    /**
     * Gets a property from a
     * @param object $object
     * @param string $prop
     * @param string|null $scope
     * @return mixed
     */
    public function get(object $object, string $prop, string $scope = null);

    /**
     * @param object $object
     * @param string $prop
     * @param $value
     * @param string|null $scope
     */
    public function set(object $object, string $prop, $value, string $scope = null): void;
}
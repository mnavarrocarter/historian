<?php

namespace Historian\Util;

use Closure;

/**
 * Class ClosurePropertyAccessor
 *
 * This property accessor allows for reading private props of a class
 * with a certain scope.
 *
 * This trick was taken from a blog post from the genius Marco Pivetta.
 *
 * @link https://ocramius.github.io/blog/accessing-private-php-class-members-without-reflection/
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
final class ClosurePropertyAccessor implements PropertyAccessor
{
    /**
     * @inheritDoc
     */
    public function has(object $object, string $prop, string $scope = null): bool
    {
        $has = Closure::fromCallable(function (string $prop): bool {
            return isset($this->{$prop});
        })->bindTo($object, $scope ?? get_class($object));
        return $has($prop);
    }

    /**
     * @inheritDoc
     */
    public function get(object $object, string $prop, string $scope = null)
    {
        $get = Closure::fromCallable( function (string $prop) {
            return $this->{$prop};
        })->bindTo($object, $scope ?? get_class($object));
        return $get($prop);
    }

    /**
     * @inheritDoc
     */
    public function set(object $object, string $prop, $value, string $scope = null): void
    {
        $set = Closure::fromCallable(function (string $prop, $value) {
            $this->{$prop} = $value;
        })->bindTo($object, $scope ?? get_class($object));
        $set($prop, $value);
    }
}
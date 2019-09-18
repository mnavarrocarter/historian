<?php

namespace Historian\Util;

use Closure;

/**
 * Class PropertyAccessor
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
class PropertyAccessor
{
    /**
     * @var callable
     */
    protected $constructor;
    /**
     * @var callable
     */
    protected $caller;
    /**
     * @var callable
     */
    protected $setter;
    /**
     * @var callable
     */
    protected $getter;
    /**
     * @var callable
     */
    protected $hasser;

    /**
     * PropertyAccessor constructor.
     */
    public function __construct()
    {
        $this->constructor = static function (string $class, array $args) {
            return new $class(...$args);
        };
        $this->caller = function (string $method, array $args) {
            return $this->{$method}(...$args);
        };
        $this->setter = function (string $prop, $value) {
            $this->{$prop} = $value;
        };
        $this->getter = function (string $prop) {
            return $this->{$prop};
        };
        $this->hasser = function (string $prop): bool {
            return isset($this->{$prop});
        };
    }

    /**
     * @param string $class
     * @param string $scope
     * @param array $args
     * @return object
     */
    public function construct(string $class, string $scope = 'static', array $args = [])
    {
        $construct = Closure::bind($this->constructor, null, $scope);
        return $construct($class, $args);
    }

    /**
     * @param $object
     * @param string $method
     * @param string $scope
     * @param array $args
     * @return mixed
     */
    public function call($object, string $method, string $scope = 'static', array $args = [])
    {
        $call = Closure::bind($this->caller, $object, $scope);
        return $call($method, $args);
    }

    /**
     * @param $object
     * @param string $prop
     * @param string|null $scope
     * @return bool
     */
    public function has($object, string $prop, string $scope = 'static'): bool
    {
        $has = Closure::bind($this->hasser, $object, $scope);
        return $has($prop);
    }

    /**
     * @param $object
     * @param string $prop
     * @param string $scope
     * @return mixed
     */
    public function get($object, string $prop, string $scope = 'static')
    {
        $getter = Closure::bind($this->getter, $object, $scope);
        return $getter($prop);
    }

    /**
     * @param $object
     * @param string $prop
     * @param $value
     * @param string $scope
     */
    public function set($object, string $prop, $value, string $scope = 'static'): void
    {
        $setter = Closure::bind($this->setter, $object, $scope);
        $setter($prop, $value);
    }
}
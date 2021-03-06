<?php
namespace ShoppingFeed\Sdk\Resource;

use ShoppingFeed\Sdk\Hal;

abstract class AbstractResource implements \JsonSerializable
{
    /**
     * @var Hal\HalResource
     */
    protected $resource;

    /**
     * @var bool
     */
    private $isPartial;

    /**
     * @param Hal\HalResource $resource
     * @param bool        $isPartial
     */
    public function __construct(Hal\HalResource $resource, $isPartial = true)
    {
        $this->resource  = $resource;
        $this->isPartial = $isPartial;
    }

    /**
     * Refresh the resource state from server data, then return it as new object
     *
     * @return static
     */
    public function refresh()
    {
        $instance = clone $this;
        $instance->initialize(true);

        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->resource->getProperties();
    }

    /**
     * @param string $property
     *
     * @return mixed|null
     */
    protected function getProperty($property)
    {
        return $this->resource->getProperty($property);
    }

    /**
     * @param string $property
     * @param        $value
     *
     * @return bool
     */
    protected function propertyMatch($property, $value)
    {
        return $this->getProperty($property) === $value;
    }

    /**
     * Get, if exists, property as Datetime object
     *
     * @param string $property
     *
     * @return \DateTimeImmutable|null
     * @throws \Exception
     */
    protected function getPropertyDatetime($property)
    {
        if ($prop = $this->resource->getProperty($property)) {
            return new \DateTimeImmutable($prop);
        }

        return null;
    }

    /**
     * @param bool $force
     *
     * @return $this
     */
    protected function initialize($force = false)
    {
        if (true === $force || true === $this->isPartial) {
            $this->resource  = $this->resource->get();
            $this->isPartial = false;
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function isPartial()
    {
        return $this->isPartial;
    }
}

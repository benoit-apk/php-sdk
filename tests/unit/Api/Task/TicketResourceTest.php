<?php
namespace ShoppingFeed\Sdk\Test\Api\Task;

use ShoppingFeed\Sdk\Api\Task\TicketResource;
use ShoppingFeed\Sdk\Test\Api\AbstractResourceTest;

class TicketResourceTest extends AbstractResourceTest
{
    public function setUp()
    {
        $this->props = [
            'id' => '123abc',
        ];
    }

    public function testGetproperty()
    {
        $this->initPropertyGetterTester();

        $instance = new TicketResource($this->propertyGetter);

        $this->assertEquals($this->props['id'], $instance->getId());
    }
}

<?php
namespace ShoppingFeed\Sdk\Test\Api\Order;

use ShoppingFeed\Sdk;

class OrderResourceTest extends Sdk\Test\Api\AbstractResourceTest
{
    public function setUp()
    {
        $this->props = [
            'id'              => 10,
            'reference'       => 'abc123',
            'storeReference'  => 'def456',
            'status'          => 'active',
            'createdAt'       => '2017-12-05',
            'updatedAt'       => '2017-12-06',
            'acknowledgedAt'  => '2017-12-07',
            'payment'         => [
                "carrier"        => "Home",
                "trackingNumber" => "94718832",
            ],
            'shipment'        => [
                "shippingAmount" => 58.8,
                "productAmount"  => 495.24,
                "totalAmount"    => 554.04,
                "currency"       => "EUR",
                "method"         => "",
            ],
            'shippingAddress' => [
                "firstName"         => "Bill",
                "lastName"          => "BOQUET",
                "company"           => "BD",
                "street"            => "10 RUE RUE DE BOULE",
                "additionalDetails" => "",
                "postalCode"        => "75000",
                "city"              => "PARIS",
                "country"           => "FR",
                "phone"             => "061234579",
                "email"             => "biletboule@mail.com",
            ],
            'billingAddress'  => [
                "firstName"         => "Bill",
                "lastName"          => "BOQUET",
                "company"           => "BD",
                "street"            => "10 RUE RUE DE BOULE",
                "additionalDetails" => "",
                "postalCode"        => "75000",
                "city"              => "PARIS",
                "country"           => "FR",
                "phone"             => "061234579",
                "email"             => "biletboule@mail.com",
            ],
        ];
    }

    public function testPropertiesGetters()
    {
        $this->initPropertyGetterTester();

        $instance = new Sdk\Api\Order\OrderResource($this->propertyGetter);

        $this->assertEquals($this->props['id'], $instance->getId());
        $this->assertEquals($this->props['reference'], $instance->getReference());
        $this->assertEquals($this->props['storeReference'], $instance->getStoreReference());
        $this->assertEquals($this->props['status'], $instance->getStatus());
        $this->assertEquals($this->props['payment'], $instance->getPaymentInformation());
        $this->assertEquals($this->props['shipment'], $instance->getShipment());
        $this->assertEquals($this->props['shippingAddress'], $instance->getShippingAddress());
        $this->assertEquals($this->props['billingAddress'], $instance->getBillingAddress());
        $this->assertEquals(date_create_immutable($this->props['createdAt']), $instance->getCreatedAt());
        $this->assertEquals(date_create_immutable($this->props['updatedAt']), $instance->getUpdateddAt());
        $this->assertEquals(date_create_immutable($this->props['acknowledgedAt']), $instance->getAcknowledgedAt());
    }

    public function testNullDates()
    {
        $this->props = [
            'updatedAt'      => null,
            'acknowledgedAt' => null,
        ];
        $this->initPropertyGetterTester();

        $instance = new Sdk\Api\Order\OrderResource($this->propertyGetter);

        $this->assertInstanceOf(\DateTimeImmutable::class, $instance->getUpdateddAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $instance->getAcknowledgedAt());
    }
}

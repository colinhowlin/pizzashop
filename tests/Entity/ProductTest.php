<?php


use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{

    public function testSetName()
    {
        $product = new Product();

        $product->setName("Test Name");

        $this->assertSame("Test Name", $product->getName());
    }

    public function testSetDescription()
    {
        $product = new Product();

        $product->setDescription("Test Product");

        $this->assertSame("Test Product", $product->getDescription());
    }

    public function testSetProductCode()
    {
        $product = new Product();

        $product->setProductCode("Test Product Code");

        $this->assertSame("Test Product Code", $product->getProductCode());
    }

    public function testSetPrice()
    {
        $product = new Product();

        $product->setPrice(11);

        $this->assertSame(11, $product->getPrice());
    }
}

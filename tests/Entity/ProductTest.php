<?php


use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{

    public function testSetName()
    {
        $product = new Product();

        $product->setName("Colin");

        $this->assertSame("Colin", $product->getName());
    }

}

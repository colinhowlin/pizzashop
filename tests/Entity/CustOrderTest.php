<?php


use App\Entity\CustOrder;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class CustOrderTest extends TestCase
{

    public function testSetUserId()
    {
        $order = new CustOrder();

        $order->setUserId(999);

        $this->assertEquals(999, $order->getUserId());
    }

    public function testSetStatus()
    {
        $order = new CustOrder();

        $order->setComments("Test Comment");

        $this->assertEquals("Test Comment", $order->getComments());
    }

    public function testSetTotalCost()
    {
        $order = new CustOrder();

        $order->setTotalCost(99);

        $this->assertEquals(99, $order->getTotalCost());
    }

    public function testSetComments()
    {
        $order = new CustOrder();

        $order->setComments("Test Comment");

        $this->assertEquals("Test Comment", $order->getComments());
    }
}

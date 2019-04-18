<?php


use App\Controller\ManagerController;
use App\Entity\CustOrder;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class ManagerControllerTest extends TestCase
{
    public function testIndex(){
        $manager = new ManagerController();


    }
}

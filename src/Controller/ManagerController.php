<?php

namespace App\Controller;

use App\Entity\CustOrder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ManagerController extends AbstractController
{
    /**
     * @Route("/manager", name="manager")
     */
    public function index()
    {
        return $this->render('manager/index.html.twig', [
            'controller_name' => 'ManagerController',
        ]);
    }

    /**
     * @Route("/manager/cancel/{id}", name="manager_cancel_order")
     *
     */
    public function cancelOrder($id){
        // get EntityManager
        $em = $this->getDoctrine()->getManager();

        // OR you can get the entity itself ( will generate a query )
        $order = $this->getDoctrine()
            ->getRepository(CustOrder::class)
            ->find($id);

        var_dump($order);

        // Remove it and flush
        $em->remove($order);
        $em->flush();

        return new Response("Order deleted!");
    }

    /**
     * @Route ("/manager/report/daily/{date}
     *
     */
    public function getDailySalesReport($date){

    }
}

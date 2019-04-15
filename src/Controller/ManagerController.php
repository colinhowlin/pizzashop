<?php

namespace App\Controller;

use App\Entity\CustOrder;
use App\Entity\CustOrderItem;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
        //get the order from $id
        $order = $this->findOrderById($id);

        // get EntityManager
        $em = $this->getDoctrine()->getManager();
        // Remove it and flush the database
        $em->remove($order);
        $em->flush();

        return new Response("Order deleted!");
    }

    /**
     * @Route ("/manager/report/daily/{date}", name="manager_daily_sales")
     *
     */
    public function getDailySalesReport($date){
        $orders = $this->getDoctrine()
            ->getRepository(CustOrder::class)
            ->findByDate($date);

        $total_sales = 0;
        $total_orders = 0;

        foreach($orders as $order) {
            $order_id = $order['id'];
            //$order_items = new CustOrderController->findByCust(CustOrderItem::class, $order_id, 'order_id');
            $order_items = $this->getDoctrine()
                ->getRepository(CustOrderItem::class)
                ->findBy(array('order_id' => $order_id));

            //get the thotal cost of each order by looping through each item
            foreach ($order_items as $item) {
                $product_id = $item->getProductId();
                $quantity = $item->getQuantity();
                $product = $this->getDoctrine()
                    ->getRepository(Product::class)
                    ->find($product_id);
                //$product = $this->findCust(Product::class, $product_id);
                $total_sales += $quantity * $product->getPrice();
            }

            $total_orders++;
        }

        return new JsonResponse(array(
            'total_orders' => $total_orders,
            'total_sales' => $total_sales,
        ));
    }

    /**
     * @Route ("/manager/report/monthly/{month}", name="manager_monthly_sales")
     *
     */
    public function getMonthlySalesReport($month){
        //get all orders
        $orders = $this->getDoctrine()
            ->getRepository(CustOrder::class)
            ->getMonthlyData($month);

        $total_sales = 0;
        $total_orders = 0;

        foreach($orders as $order) {
            $order_id = $order['id'];
            //$order_items = new CustOrderController->findByCust(CustOrderItem::class, $order_id, 'order_id');
            $order_items = $this->getDoctrine()
                ->getRepository(CustOrderItem::class)
                ->findBy(array('order_id' => $order_id));

            //get the thotal cost of each order by looping through each item
            foreach ($order_items as $item) {
                $product_id = $item->getProductId();
                $quantity = $item->getQuantity();
                $product = $this->getDoctrine()
                    ->getRepository(Product::class)
                    ->find($product_id);
                //$product = $this->findCust(Product::class, $product_id);
                $total_sales += $quantity * $product->getPrice();
            }

            $total_orders++;
        }

        return new JsonResponse(array(
            'total_orders' => $total_orders,
            'total_sales' => $total_sales,
        ));
    }

    /**
     *
     * Helper functions
     *
     */

    /**
     * @param $id
     * @return object|null
     *
     * Retrieves order from the database given the id
     */
    public function findOrderById($id){
        $order = $this->getDoctrine()
            ->getRepository(CustOrder::class)
            ->find($id);

        return $order;
    }
}

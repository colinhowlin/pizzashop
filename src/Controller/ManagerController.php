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

        $totals = $this->findTotalSales($orders);

        return new JsonResponse(array(
            'total_orders' => $totals['total_orders'],
            'total_sales' => $totals['total_sales'],
        ));
    }

    /**
     * @Route ("/manager/report/monthlysummary", name="manager_monthly_summary")
     *
     * This route is called to build the monthly summary table
     */
    public function monthlySummary(){
        $orders = $this->getDoctrine()
            ->getRepository(CustOrder::class)
            ->getMonthlySummary();

        $monthlySales = [
            "jan" => 0,
            "feb" => 0,
            "mar" => 0,
            "apr" => 0,
            "may" => 0,
            "jun" => 0,
            "jul" => 0,
            "aug" => 0,
            "sep" => 0,
            "oct" => 0,
            "nov" => 0,
            "dec" => 0
        ];

        foreach ($orders as $order){
            switch($order['nmonth']){
                case 1:
                    $monthlySales['jan'] += $order['total_cost'];
                    break;
                case 2:
                    $monthlySales['feb'] += $order['total_cost'];
                    break;
                case 3:
                    $monthlySales['mar'] += $order['total_cost'];
                    break;
                case 4:
                    $monthlySales['apr'] += $order['total_cost'];
                    break;
                case 5:
                    $monthlySales['may'] += $order['total_cost'];
                    break;
                case 6:
                    $monthlySales['jun'] += $order['total_cost'];
                    break;
                case 7:
                    $monthlySales['jul'] += $order['total_cost'];
                    break;
                case 8:
                    $monthlySales['aug'] += $order['total_cost'];
                    break;
                case 9:
                    $monthlySales['sep'] += $order['total_cost'];
                    break;
                case 10:
                    $monthlySales['oct'] += $order['total_cost'];
                    break;
                case 11:
                    $monthlySales['nov'] += $order['total_cost'];
                    break;
                case 12:
                    $monthlySales['dec'] += $order['total_cost'];
                    break;
            }
        }

        return new JsonResponse(array(
            'monthly_sales' => $monthlySales,
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

        $totals = $this->findTotalSales($orders);

        return new JsonResponse(array(
            'total_orders' => $totals['total_orders'],
            'total_sales' => $totals['total_sales'],
        ));
    }

    /**
     * @Route ("/manager/report/weekly/{week}", name="manager_weekly_sales")
     *
     */
    public function getWeeklySalesReport($week){
        //get all orders
        $orders = $this->getDoctrine()
            ->getRepository(CustOrder::class)
            ->getWeeklyData($week);

        $totals = $this->findTotalSales($orders);

        return new JsonResponse(array(
            'total_orders' => $totals['total_orders'],
            'total_sales' => $totals['total_sales'],
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

    /**
     * @param $orders - array of orders from database
     * @return array - array of total sales and total orders
     *
     * Calculates total sales and total orders from an array of orders
     */
    public function findTotalSales($orders){
        $totals = ['total_sales' => 0, 'total_orders' => 0];

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
                $totals['total_sales'] += $quantity * $product->getPrice();
            }

            $totals['total_orders']++;
        }

        return $totals;
    }
}

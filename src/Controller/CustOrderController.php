<?php

namespace App\Controller;

use App\Entity\CustOrderItem;
use App\Entity\CustOrder;
use App\Entity\Product;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;


class CustOrderController extends AbstractController
{

    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }

    /**
     * @Route("/order", name="order")
     */
    public function index(UserInterface $user)
    {
        //get $request items and find type of request
        $request = Request::createFromGlobals();
        $delivery_address = $request->request->get('delivery_address', 'none');
        $comments = $request->request->get('comments', 'none');

        //get the orderDetails JSON from $REQUEST and decode it
        $orderedItems = json_decode($request->request->get('orderedItems', 'none'));

        //set the current time for timestamp
        $date = date('Y-m-d H:i:s');
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $date);

        //get current userId
        $user_id = $user->getId();

        //insert the order into the database
        $custOrder = new CustOrder();
        $custOrder->setUserId($user_id);
        $custOrder->setDeliveryAddress($delivery_address);
        $custOrder->setStatus("In Progress");
        $custOrder->setComments($comments);
        $custOrder->setTimestamp($datetime);

        //Get the entityManager to add the items to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($custOrder);
        $entityManager->flush();

        //after flushing, get the order ID
        $orderID = $custOrder->getId();

        //add each item in the orderItems JSON to the orderItems table
        foreach($orderedItems as $item){
            //find product_id from product_code supplied - from Product table
            $repo = $entityManager->getRepository(Product::class);
            $product = $repo->findOneBy(['product_code' => $item->product_code]);

            //add items to the orderItem table
            $custOrderItem = new CustOrderItem();
            $custOrderItem->setProductId($product->getId());
            $custOrderItem->setQuantity($item->qty);
            $custOrderItem->setOrderId($orderID);

            $entityManager->persist($custOrderItem);
            $entityManager->flush();
        }

        //Add code here to redirect and show confirmation of order acceptance

        return $this->render('cust_order/orderconfirmation.html.twig');
    }

    /**
     * @Route("/order/confirmation", name="order_confirmation")
     */
    public function orderConfirmed(){
        return $this->render('cust_order/orderconfirmation.html.twig');
    }

    /**
     * @Route("/order/view", name="order/view")
     *
     */
    public function viewOrders(UserInterface $user){
        $user_id = $user->getId();

        if ($this->security->isGranted('ROLE_DRIVER')) {
            $orders = $this->getDoctrine()
                ->getRepository(CustOrder::class)
                ->findAll();
        }
        else if ($this->security->isGranted('ROLE_USER')) {
            $orders = $this->getDoctrine()
                ->getRepository(CustOrder::class)
                ->findBy(array('user_id' => $user_id));
        }

        $ordersArray = array();

        foreach($orders as $order) {
            $order_id = $order->getId();
            $total_cost = 0;
            $order_items = $this->getDoctrine()
                ->getRepository(CustOrderItem::class)
                ->findBy(array('order_id' => $order_id));

            foreach($order_items as $item){
                $product_id = $item->getProductId();
                $quantity = $item->getQuantity();

                $product = $this->getDoctrine()
                    ->getRepository(Product::class)
                    ->find($product_id);

                $total_cost += $quantity * $product->getPrice();
            }

            $ordersArray[] = array(
                'id' => $order_id,
                'delivery_address' => $order->getDeliveryAddress(),
                'status' => $order->getStatus(),
                'comments' => $order->getcomments(),
                'total_cost' => $total_cost,
                'timestamp' => $order->getTimestamp()->format('Y-m-d H:i:s'),
            );
        }

        $response = new Response(json_encode($ordersArray));

        return $response;
    }

    /**
     * @Route ("order/view/{id}", name="order_view_by_id")
     *
     */
    public function viewOrderById($id){
        $order_items = $this->getDoctrine()
            ->getRepository(CustOrderItem::class)
            ->findBy(array('order_id' => $id));

        $custOrderDetailsArray = array();

        foreach($order_items as $item){
            //$custOrderDetails = new CustOrderDetails();
            $product_id = $item->getProductId();

            $product = $this->getDoctrine()
                ->getRepository(Product::class)
                ->find($product_id);

            $custOrderDetailsArray[] = array(
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'quantity' => $item->getQuantity(),
                'price' => $product->getPrice(),
            );
        }

        $response = new Response(json_encode($custOrderDetailsArray));

        return $response;
    }

    /**
     * @Route ("/order/dispatch/{id}", name="order_dispatch_id")
     *
     */
    public function dispatchOrder($id){
        $order = $this->getDoctrine()
            ->getRepository(CustOrder::class)
            ->find($id);

        $order->setStatus("Dispatched");

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($order);
        $entityManager->flush();

        return new Response("Order dispatched!");
    }

    /**
     * @Route ("/order/complete/{id}", name="order_complete_id")
     *
     */
    public function completeOrder($id){
        $order = $this->getDoctrine()
            ->getRepository(CustOrder::class)
            ->find($id);

        $order->setStatus("Completed");

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($order);
        $entityManager->flush();

        return new Response("Order Delivered!");
    }
}

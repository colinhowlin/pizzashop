<?php

namespace App\Controller;

use App\Entity\CustOrderItem;
use App\Entity\CustOrder;
use App\Entity\Product;
use DateTime;
use phpDocumentor\Reflection\Types\Resource_;
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
        //get delivery address and comments from $Request
        $request = Request::createFromGlobals();
        $delivery_address = $request->request->get('delivery_address', 'none');
        $comments = $request->request->get('comments', 'none');
        $total_cost = $request->request->get('total_cost', 'none');

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
        $custOrder->setTotalCost($total_cost);

        //persist order to the database
        $this->persistToDb($custOrder);

        //after flushing, get the order ID
        $orderID = $custOrder->getId();

        //add each item in the orderItems JSON to the orderItems table
        foreach($orderedItems as $item){
            //find product_id from product_code supplied - from Product table
            $repo = $this->getDoctrine()->getManager()->getRepository(Product::class);
            $product = $repo->findOneBy(['product_code' => $item->product_code]);

            //add items to the orderItem table
            $custOrderItem = new CustOrderItem();
            $custOrderItem->setProductId($product->getId());
            $custOrderItem->setQuantity($item->qty);
            $custOrderItem->setOrderId($orderID);

            //persist to DB
            $this->persistToDb($custOrderItem);
        }

        return new Response($orderID);
    }

    /**
     * @Route("/order/confirmation", name="order_confirmation")
     */
    //public function orderConfirmed(){
    //    $request = Request::createFromGlobals();
    //    //var_dump($request);
    //    $order_id = $request->request->get('order_id', 'none');
    //    echo($order_id);

    //    return $this->render('cust_order/orderconfirmation.html.twig',
    //        array(
    //            'order_id' => $order_id
    //        ));
   // }

    /**
     * @Route("/order/view", name="order/view")
     *
     */
    public function viewOrders(UserInterface $user){
        $user_id = $user->getId();

        //if user has Driver role, show all orders
        if ($this->security->isGranted('ROLE_DRIVER')) {
            $orders = $this->findAllCust(CustOrder::class);
        }
        //if user is a customer, show only their orders
        else if ($this->security->isGranted('ROLE_USER')) {
            $orders = $this->findByCust(CustOrder::class, $user_id, 'user_id');
        }

        $ordersArray = array();

        //loop through orders to build the ordered items
        foreach($orders as $order) {
            $order_id = $order->getId();
            $total_cost = 0;
            $order_items = $this->findByCust(CustOrderItem::class, $order_id, 'order_id');

            //get the thotal cost of each order by looping through each item
            foreach($order_items as $item){
                $product_id = $item->getProductId();
                $quantity = $item->getQuantity();
                $product = $this->findCust(Product::class, $product_id);
                $total_cost += $quantity * $product->getPrice();
            }

            //build the array to be pased to the view
            $ordersArray[] = array(
                'id' => $order_id,
                'delivery_address' => $order->getDeliveryAddress(),
                'status' => $order->getStatus(),
                'comments' => $order->getcomments(),
                'total_cost' => $total_cost,
                'timestamp' => $order->getTimestamp()->format('Y-m-d H:i:s'),
            );
        }

        //encode array as json
        $response = new Response(json_encode($ordersArray));

        return $response;
    }

    /**
     * @Route ("order/view/{id}", name="order_view_by_id")
     *
     */
    public function viewOrderById($id){
        //retrieve the ordered items from the database for the given ID
        $order_items = $this->findByCust(CustOrderItem::class, $id, 'order_id');
        $custOrderDetailsArray = array();

        //loop through the ordered items and retrieve the Product object of each
        foreach($order_items as $item){
            $product_id = $item->getProductId();
            $product = $this->findCust(Product::class, $product_id);

            //build array to pass to the view
            $custOrderDetailsArray[] = array(
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'quantity' => $item->getQuantity(),
                'price' => $product->getPrice(),
            );
        }

        //Encode the array as a JSON for easy parsing
        $response = new Response(json_encode($custOrderDetailsArray));

        return $response;
    }

    /**
     * @Route ("/order/dispatch/{id}", name="order_dispatch_id")
     *
     */
    public function dispatchOrder($id){
        //Retrieve order from db and set status to Dispatched
        $order = $this->findCust(CustOrder::class, $id);
        $order->setStatus("Dispatched");
        $this->persistToDb($order);

        return new Response("Order dispatched!");
    }

    /**
     * @Route ("/order/complete/{id}", name="order_complete_id")
     *
     */
    public function completeOrder($id){
        //retrieve the order from DB and set status to completed
        $order = $this->findCust(CustOrder::class, $id);
        $order->setStatus("Completed");
        $this->persistToDb($order);

        return new Response("Order Delivered!");
    }

    /**
     *
     * Helper methods to retrieve data from database
     *
     */

    /**
     * @param $entity
     * @return object[]
     *
     * Retrieves all records of passed Entity
     */
    public function findAllCust($entity){
        $results = $this->getDoctrine()
            ->getRepository($entity)
            ->findAll();

        return $results;
    }

    /**
     * @param $entity
     * @param $id
     * @param $idString
     * @return object[]
     *
     * Retrieves records of passed entity with passed id
     */
    public function findByCust($entity, $id, $idString){
        $results = $this->getDoctrine()
            ->getRepository($entity)
            ->findBy(array($idString => $id));

        return $results;
    }

    /**
     * @param $entity
     * @param $field
     * @return object|null
     *
     * Retrieves records from entity from passed field
     */
    public function findCust($entity, $field){
        $results = $this->getDoctrine()
            ->getRepository($entity)
            ->find($field);

        return $results;
    }

    /**
     * @param $entity
     *
     * Persists the passed object to the database
     */
    public function persistToDb($entity){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }
}

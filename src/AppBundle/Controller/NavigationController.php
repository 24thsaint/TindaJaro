<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Message;
use AppBundle\Entity\Rating;
use AppBundle\Controller\AjaxController;

class NavigationController extends Controller {

    /**
    * @Route("/test")
    */
    public function test() {
        return $this->render(
            'Homepage/Test.html.twig'
        );
    }

    /**
    * @Route("/overview")
    */
    public function overviewAction() {

        $timeGreet = "";
        $hourNow = date("H");

        if ($hourNow < 12) {
            $timeGreet = "Good Morning";
        } else if ($hourNow == 12) {
            $timeGreet = "Good Noon";
        } else if ($hourNow > 12 && $hourNow <= 18) {
            $timeGreet = "Good Afternoon";
        } else {
            $timeGreet = "Good Evening";
        }

        $user = $this->getUser();

        return $this->render(
            'Homepage/Overview.html.twig',
            array(
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastName(),
                'mobilenumber' => $user->getMobileNumber(),
                'email' => $user->getEmail(),
                'homeaddress' => $user->getHomeAddress(),
                'greeting' => $timeGreet . ', ' . $user->getFirstName() . '!'
            )
        );
    }

    /**
    * @Route("/product/view/{productId}")
    */
    public function productViewAction($productId) {

        $repository = $this->getDoctrine()->getRepository("AppBundle:Product");
        $product = $repository->findOneByproductId($productId);
        $storeRepository = $this->getDoctrine()->getRepository("AppBundle:Store");
        $store = $storeRepository->findOneBystoreId($product->getStoreId());

        $userId = $this->getUser()->getId();
        $vendorId = $store->getVendorId();

        $userRepository = $this->getDoctrine()->getRepository("AppBundle:User");
        $user = $userRepository->findOneById($vendorId);

        return $this->render(
            'Store/ViewProduct.html.twig',
            array(
                'product' => $product,
                'userId' => $userId,
                'vendorId' => $vendorId,
                'vendorName' => $user->getFirstname().' '.$user->getLastName(),
                'vendorAddress' => $user->getHomeAddress(),
                'vendorMobileNumber' => $user->getMobileNumber()
            )
        );
    }

    /**
    * @Route("/browse/stores")
    */
    public function allStoresBrowseFunction() {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Store");

        $stores = $repository->findByisStoreStatusOpen(true);

        foreach ($stores as $store) {
            $rating = $this->getStoreRating($store->getStoreId());
            $store->storeRating = $rating;
        }

        return $this->render(
            'Homepage/BrowseStores.html.twig',
            array(
                'stores' => $stores
            )
        );
    }

    /**
    * @Route("/browse/stores/{storeId}")
    */
    public function singleStoreBrowseAction($storeId) {
        $storeRepository = $this->getDoctrine()->getRepository("AppBundle:Store");
        $store = $storeRepository->findOneBystoreId($storeId);

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            '
            SELECT p FROM AppBundle:Product p WHERE p.storeId = :storeId AND p.productQuantity > 0 ORDER BY p.productId DESC
            '
        )->setParameter('storeId', $storeId);

        $products = $query->getResult();

        $storeRating = $this->getStoreRating($storeId);

        $vendorId = $store->getVendorId();

        return $this->render(
            'Homepage/ViewStoreSingle.html.twig',
            array(
                'products' => $products,
                'storename' => $store->getStoreName(),
                'storeimage' => $store->getStoreImage(),
                'storeId' => $store->getStoreId(),
                'storeRating' => $storeRating,
                'vendorId' => $vendorId
            )
        );
    }

    protected function getStoreRating($storeId) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT r FROM AppBundle:Rating r WHERE r.storeId=:storeId')->setParameter('storeId', $storeId);
        $result = $query->getResult();

        $resultCount = count($result);

        if ($resultCount <= 0) {
            return 0;
        }

        $total = 0;

        foreach ($result as $rate) {
            $total += $rate->getRating();
        }

        $averageRate = $total / $resultCount;
        return $averageRate;
    }

    /**
    * @Route("/rate/{storeId}/{rating}")
    */
    public function rateAction($storeId, $rating) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
        'SELECT r FROM AppBundle:Rating r WHERE r.customerId=:customerId AND r.storeId=:storeId'
        )->setParameter("customerId", $this->getUser()->getId())->setParameter("storeId", $storeId);
        $resultCount = 0;

        try {
            $result = $query->getSingleResult();
            $resultCount = count($rating);
        } catch(\Doctrine\ORM\NoResultException $e) {
            $resultCount = 0;
        }

        if ($resultCount > 0) {
            $result->setRating($rating);
        } else {
            $newRating = new Rating();
            $newRating->setStoreId($storeId);
            $newRating->setRating($rating);
            $newRating->setCustomerId($this->getUser()->getId());
            $em->persist($newRating);
        }
        $em->flush();

        return new Response($this->getStoreRating($storeId));
    }

    /**
    * @Route("/transaction-history")
    */
    public function transactionHistoryAction() {
        $transactions = array();
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            '
                SELECT t FROM AppBundle:Order t WHERE t.customerId=:customerId ORDER BY t.transactionDate DESC
            '
        )->setParameter("customerId", $this->getUser()->getId());
        $orders = $query->getResult();

        $productRepository = $this->getDoctrine()->getRepository("AppBundle:Product");

        foreach ($orders as $order) {
            $transaction = array();
            $product = $productRepository->findOneByproductId($order->getProductId());

            $transaction['transactionDate'] = date_format($order->getTransactionDate(), DATE_RFC850);
            $transaction['productName'] = $product->getProductName();
            $transaction['productPrice'] = $product->getProductPrice();
            $transaction['orderQuantity'] = $order->getQuantity();
            $transaction['status'] = $order->getOrderStatus();
            $transactions[] = $transaction;
        }

        return $this->render('Homepage/TransactionHistory.html.twig', array('transactions' => $transactions));
    }

    /**
    * @Route("/chat")
    */
    public function chatNavigateAction() {
        return $this->render('Homepage/Chat.html.twig');
    }

}

?>

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Message;
use AppBundle\Entity\Rating;
use AppBundle\Entity\Product;
use AppBundle\Entity\Store;
use AppBundle\Controller\AjaxController;

class NavigationController extends Controller {

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
                'user' => $this->getUser(),
                'greeting' => $timeGreet . ', ' . $user->getFirstName() . '!'
            )
        );
    }

    /**
    * @Route("/product/view/{product}")
    */
    public function productViewAction(Product $product) {
        return $this->render(
            'Store/ViewProduct.html.twig',
            array(
                'product' => $product,
                'user' => $this->getUser()
            )
        );
    }

    /**
    * @Route("/browse/stores")
    */
    public function browseOpenStoresAction() {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Store");
        $stores = $repository->findByisOpen(true);

        return $this->render(
            'Homepage/BrowseStores.html.twig',
            array(
                'stores' => $stores
            )
        );
    }

    /**
    * @Route("/browse/stores/{store}")
    */
    public function singleStoreBrowseAction(Store $store) {
        $productRepository = $this->getDoctrine()->getRepository("AppBundle:Product");
        $products = $productRepository->findAllActiveProductsInASpecificStore($store);

        return $this->render(
            'Homepage/ViewStoreSingle.html.twig',
            array(
                'store' => $store,
                'products' => $products
            )
        );
    }

    /**
    * @Route("/shipments/view")
    */
    public function viewShippedAction() {
        $store = $this->getUser()->getStore();
        $shippedOrders = $store->getAllShippedOrders();

        return $this->render(
            'Store/MyStoreShipped.html.twig',
            array(
                'shippedOrders' => $shippedOrders
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
        $transactions = $this->getUser()->getOrders();
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

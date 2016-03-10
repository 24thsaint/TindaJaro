<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Store;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\StoreDetailType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\FileSystem\Exception\IOExceptionInterface;

class MyStoreController extends Controller {
    /**
    * @Route("vendor/mystore")
    */
    public function myStoreAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            '
                SELECT p FROM AppBundle:Product p WHERE p.storeId=:storeId AND p.isActive=true
            '
        )->setParameter('storeId', $this->getUser()->getStoreId());
        $products = $query->getResult();

        $storeRepository = $this->getDoctrine()->getRepository('AppBundle:Store');
        $store = $storeRepository->findOneByvendorId($this->getUser()->getId());

        $storeTmp = new Store();
        $form = $this->createForm(StoreDetailType::class, $storeTmp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $store->setStoreName($storeTmp->getStoreName());
            $store->setStoreDescription($storeTmp->getStoreDescription());

            // handle image upload
            if ($storeTmp->getStoreImage()) {
                $file = $storeTmp->getStoreImage();
                $fileName = md5(uniqid()).'.'.$file->guessExtension(); // we do not want to save the same filenames -_-
                $imagesDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/stores/images';
                if ($store->getStoreImage() == 'storeDefault.jpeg') {
                    // omg do not delete the default image, other new stores need it.
                } else {
                    $this->deleteFile($imagesDir.'/'.$store->getStoreImage());
                }
                $store->setStoreImage($fileName);
                $file->move($imagesDir, $fileName);
            } else {
                // do nothing, maintain current image file
            }
            // handler end

            $em = $this->getDoctrine()->getManager();

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your changes were successfully saved!'
            );

            return $this->redirect('/vendor/mystore');
        }

        return $this->render(
            'Homepage/MyStore.html.twig',
            array(
                'products' => $products,
                'storename' => $store->getStoreName(),
                'storedescription' => $store->getStoreDescription(),
                'isOpen' => $store->getIsStoreStatusOpen(),
                'form' => $form->createView()
            )
        );
    }

    protected function deleteFile($filePath) {
        $fs = new Filesystem();
        if ($fs->exists($filePath)) {
            $fs->remove($filePath);
        }
    }

    /**
    * @Route("/vendor/store-open")
    */
    public function storeOpenAction() {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Store");
        $store = $repository->findOneByvendorId($this->getUser()->getId());

        $em = $this->getDoctrine()->getManager();

        $store->setIsStoreStatusOpen(true);

        $em->flush($store);

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Your store was successfully opened!'
        );

        return $this->redirect('/vendor/mystore');
    }

    /**
    * @Route("/vendor/store-close")
    */
    public function storeCloseAction() {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Store");
        $store = $repository->findOneByvendorId($this->getUser()->getId());

        $em = $this->getDoctrine()->getManager();

        $store->setIsStoreStatusOpen(false);

        $em->flush($store);

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Your store was successfully closed!'
        );

        return $this->redirect('/vendor/mystore');
    }

    /**
    * @Route("/vendor/view-pending")
    */
    public function viewPendingAction() {
        $vendorId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            '
            SELECT o FROM AppBundle:Order o WHERE o.vendorId=:vendorId AND o.orderStatus=\'CHECKED-OUT\' ORDER BY o.transactionDate ASC, o.customerId ASC
            '
        )->setParameter('vendorId', $vendorId);
        $dbOrders = $query->getResult();

        // From here onwards, everything seems pretty hackish </3 ==================

        $orderArray = array();
        $lastOrderId = -1;

        foreach ($dbOrders as $order) {
            if ($lastOrderId != $order->getCustomerId()) {
                $lastOrderId = $order->getCustomerId();
                $orderArray[] = $lastOrderId;
            }
        }

        $groupedOrders = array();

        foreach ($orderArray as $customerId) {
            $user = array();

            $userQuery = $em->createQuery(
                '
                SELECT u FROM AppBundle:User u WHERE u.id=:customerId
                '
            )->setParameter('customerId', $customerId);

            $userData = $userQuery->getSingleResult();

            $user['group']['customerName'] = $userData->getFirstName().' '.$userData->getLastName();
            $user['group']['customerAddress'] = $userData->getHomeAddress();
            $user['group']['customerContactNumber'] = $userData->getMobileNumber();

            $orderQuery = $em->createQuery(
                '
                SELECT o FROM AppBundle:Order o WHERE o.customerId=:customerId AND o.vendorId=:vendorId AND o.orderStatus=\'CHECKED-OUT\'
                '
            )->setParameter('customerId', $customerId)->setParameter('vendorId', $vendorId);

            $customerOrders = $orderQuery->getResult();

            $grandTotal = 0;

            foreach($customerOrders as $customerOrder) {

                $productId = $customerOrder->getProductId();
                $productQuery = $em->createQuery(
                    '
                    SELECT p FROM AppBundle:Product p WHERE p.productId=:productId
                    '
                )->setParameter('productId', $productId);

                $product = $productQuery->getSingleResult();

                $orders = array();

                $orders['orderDate'] = $customerOrder->getTransactionDate();
                $orders['productName'] = $product->getProductName();
                $orders['productPrice'] = $product->getProductPrice();
                $orders['orderId'] = $customerOrder->getOrderId();
                $user['group']['customerId'] = $customerOrder->getCustomerId();
                $orders['quantity'] = $customerOrder->getQuantity();
                $user['group']['orders'][] = $orders;
                $grandTotal += $orders['productPrice'] * $orders['quantity'];
            }
            $user['group']['grandTotal'] = $grandTotal;
            $groupedOrders[] = $user;
        }

        // End hackish stuff ========================

        return $this->render(
            'Store/MyStorePending.html.twig',
            array(
                'groupedOrders' => $groupedOrders,
                'orderArray' => $orderArray
            )
        );
    }

    /**
    * @Route("/shipments/view")
    */
    public function viewShippedAction() {
        $storeId = $this->getUser()->getStoreId();

        $storeRepository = $this->getDoctrine()->getRepository("AppBundle:Store");
        $store = $storeRepository->findOneBystoreId($storeId);

        $vendorId = $store->getVendorId();

        $em = $this->getDoctrine()->getManager();

        $query = null;

        if ($this->getUser()->getId() == $store->getVendorId()) {
            $query = $em->createQuery(
                '
                SELECT o FROM AppBundle:Order o WHERE o.vendorId=:vendorId AND o.orderStatus=\'ACCEPTED BY VENDOR: Being Shipped\' OR o.orderStatus=\'DELIVERED\' ORDER BY o.transactionDate DESC, o.customerId ASC
                '
            )->setParameter('vendorId', $vendorId);
        } else {
            $query = $em->createQuery(
                '
                SELECT o FROM AppBundle:Order o WHERE o.vendorId=:vendorId AND o.orderStatus=\'ACCEPTED BY VENDOR: Being Shipped\' ORDER BY o.transactionDate DESC, o.customerId ASC
                '
            )->setParameter('vendorId', $vendorId);
        }

        $dbOrders = $query->getResult();

        // From here onwards, everything seems pretty hackish </3 ==================

        $orderArray = array();
        $lastOrderId = -1;

        // Query how many users have ordered stuff
        foreach ($dbOrders as $order) {
            if ($lastOrderId != $order->getCustomerId()) {
                $lastOrderId = $order->getCustomerId();
                $orderArray[] = $lastOrderId;
            }
        }
        // end --------

        $groupedOrders = array(); // array that will hold a list of users with their orders

        // Group all orders to the customer (one customer has many orders)
        foreach ($orderArray as $customerId) {
            $user = array(); // array that will hold a user with corresponding orders

            // get customer data first -------------
            $userQuery = $em->createQuery(
                '
                SELECT u FROM AppBundle:User u WHERE u.id=:customerId
                '
            )->setParameter('customerId', $customerId);

            $userData = $userQuery->getSingleResult();

            $user['group']['customerId'] = $userData->getId();
            $user['group']['customerName'] = $userData->getFirstName().' '.$userData->getLastName();
            $user['group']['customerAddress'] = $userData->getHomeAddress();
            $user['group']['customerContactNumber'] = $userData->getMobileNumber();
            $user['group']['transactionDate'] = null;
            // ----------------------------------------

            // order query depends on logged in user (different displays for Vendor and Delivery Guy) -----------
            $orderQuery = null;

            if ($this->getUser()->getId() == $store->getVendorId()) {
                // display of Vendor, everything is seen --------------------------
                $orderQuery = $em->createQuery(
                    '
                    SELECT o FROM AppBundle:Order o WHERE o.customerId=:customerId AND o.vendorId=:vendorId AND (o.orderStatus=\'ACCEPTED BY VENDOR: Being Shipped\' OR o.orderStatus=\'DELIVERED\')
                    '
                )->setParameter('customerId', $customerId)->setParameter('vendorId', $vendorId);
            } else {
                // display of Delivery Guy, only the pending-for-delivery stuff are seen -------------
                $orderQuery = $em->createQuery(
                    '
                    SELECT o FROM AppBundle:Order o WHERE o.customerId=:customerId AND o.vendorId=:vendorId AND o.orderStatus=\'ACCEPTED BY VENDOR: Being Shipped\'
                    '
                )->setParameter('customerId', $customerId)->setParameter('vendorId', $vendorId);
            }

            $customerOrders = $orderQuery->getResult();

            $grandTotal = 0;

            // orders of customer iteration -------------------------
            foreach($customerOrders as $customerOrder) {
                $orders = array(); // array that will hold the orders

                $productId = $customerOrder->getProductId(); //productId from customer order
                $productQuery = $em->createQuery(
                    '
                    SELECT p FROM AppBundle:Product p WHERE p.productId=:productId
                    '
                )->setParameter('productId', $productId);

                $product = $productQuery->getSingleResult();

                // try {
                //     $product = $productQuery->getSingleResult();
                // } catch (\Doctrine\ORM\NoResultException $e) {
                //     break;
                //
                //     $this->get('session')->getFlashBag()->add(
                //         'error',
                //         'No shipped orders!'
                //     );
                //
                //     return $this->redirect('/shipments/view');
                // }

                if ($customerOrder->getOrderStatus() == 'DELIVERED') {
                    // delivery guy's name ------------------------------
                    $userRepository = $this->getDoctrine()->getRepository("AppBundle:User");
                    $userData = $userRepository->findOneByid($customerOrder->getDeliveryGuy());
                    $deliveryGuy = $userData->getFirstName() . " " . $userData->getLastName();
                    $user['group']['deliveryGuy'] = $deliveryGuy;

                    $orders['status'] = $customerOrder->getOrderStatus() . " by " . $deliveryGuy;
                } else {
                    $orders['status'] = $customerOrder->getOrderStatus();
                }

                $orders['transactionDate'] = date_format($customerOrder->getTransactionDate(), DATE_RFC850);
                $orders['productName'] = $product->getProductName();
                $orders['productPrice'] = $product->getProductPrice();
                $orders['orderId'] = $customerOrder->getOrderId();
                $orders['quantity'] = $customerOrder->getQuantity();

                // overwrites are fine, we really just need the latest data information
                $user['group']['customerId'] = $customerOrder->getCustomerId();
                $user['group']['status'] = $customerOrder->getOrderStatus();
                $user['group']['transactionDate'] = date_format($customerOrder->getTransactionDate(), DATE_RFC850);

                $user['group']['orders'][] = $orders; // list of orders

                $grandTotal += $orders['productPrice'] * $orders['quantity'];
            }

            $user['group']['grandTotal'] = $grandTotal;

            $groupedOrders[] = $user; // a user with a list of his orders
        }

        // End hackish stuff ========================

        return $this->render(
            'Store/MyStoreShipped.html.twig',
            array(
                'groupedOrders' => $groupedOrders,
                'orderArray' => $orderArray
            )
        );
    }
}

?>

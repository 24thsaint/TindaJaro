<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\ProductType;
use AppBundle\Form\UserType;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\FileSystem\Exception\IOExceptionInterface;

class VendorController extends Controller {

    /**
    * @Route("/vendor/add-product")
    */
    public function addProductAction(Request $request) {
        $product = new Product();
        $product->setStoreId($this->getUser()->getStoreId());
        $product->setActive(true);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // handle image upload
            if ($product->getProductImage()) {
                $file = $product->getProductImage();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $imagesDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/products/images';

                $this->deleteFile($imagesDir.'/'.$product->getProductImage());

                $product->setProductImage($fileName);
                // upload image to server
                $file->move($imagesDir, $fileName);
            } else {
                $product->setProductImage('productDefault.jpeg');
            }
            // handler end

            $product->setStoreId($this->getUser()->getStoreId());

            // persist the image location
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your product '. $product->getProductName() .' was successfully added to your store!'
            );

            return $this->redirect('/vendor/mystore');
        }

        return $this->render(
            'Store/AddProduct.html.twig',
            array(
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
    * @Route("/vendor/ship/{customerId}")
    */
    public function shipAction($customerId) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            '
            SELECT o FROM AppBundle:Order o WHERE o.customerId=:customerId AND o.vendorId=:vendorId AND o.orderStatus=\'CHECKED-OUT\'
            '
        )->setParameter('customerId', $customerId)->setParameter('vendorId', $this->getUser()->getId());

        $orders = $query->getResult();

        $notice = "The status of Order ";

        foreach ($orders as $order) {
            $order->setOrderStatus("ACCEPTED BY VENDOR: Being Shipped");
            $now = new \DateTime();
            $order->setTransactionDate($now);
            $em->flush();
            $notice = $notice . "#" . $order->getOrderId() . " ";
        }

        $notice = $notice . "has been changed to \"ACCEPTED BY VENDOR: Being Shipped\" successfully!";

        $this->get('session')->getFlashBag()->add(
            'notice',
            $notice
        );

        return $this->redirect('/vendor/view-pending');
    }

    /**
    * @Route("/vendor/edit/{productId}")
    */
    public function editProductAction($productId, Request $request) {
        $productRepository = $this->getDoctrine()->getRepository("AppBundle:Product");
        $oldProduct = $productRepository->findOneByproductId($productId);

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // handle image upload
            if ($product->getProductImage()) {
                $file = $product->getProductImage();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                $imagesDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/products/images';

                if ($oldProduct->getProductImage() != 'productDefault.jpeg') {
                    $this->deleteFile($imagesDir.'/'.$oldProduct->getProductImage());
                }

                $oldProduct->setProductImage($fileName);
                // upload image to server
                $file->move($imagesDir, $fileName);
            }

            // update stuff

            $oldProduct->setProductName($product->getProductName());
            $oldProduct->setProductQuantity($product->getProductQuantity());
            $oldProduct->setProductPrice($product->getProductPrice());
            $oldProduct->setProductDescription($product->getProductDescription());

            $em = $this->getDoctrine()->getManager();

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your product '. $product->getProductName() .' was successfully edited!'
            );

            return $this->redirect('/vendor/mystore');
        }

        return $this->render(
            'Store/EditProduct.html.twig',
            array(
                'form' => $form->createView(),
                'productName' => $oldProduct->getProductName(),
                'productQuantity' => $oldProduct->getProductQuantity(),
                'productPrice' => $oldProduct->getProductPrice(),
                'productDescription' => $oldProduct->getProductDescription()
            )
        );
    }

    /**
    * @Route("/vendor/delete-product/{productId}")
    */
    public function deleteProduct($productId) {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            '
            SELECT o FROM AppBundle:Order o WHERE o.productId=:productId AND o.vendorId=:vendorId AND o.orderStatus=\'CHECKED-OUT\'
            '
        )->setParameter('productId', $productId)->setParameter('vendorId', $this->getUser()->getId());

        $productQuery = $em->createQuery(
            '
            SELECT p FROM AppBundle:Product p WHERE p.storeId=:storeId AND p.productId=:productId
            '
        )->setParameter('storeId', $this->getUser()->getStoreId())->setParameter('productId', $productId);

        $product = $productQuery->getSingleResult();

        $order = null;

        try {
            $order = $query->getSingleResult();

            $this->get('session')->getFlashBag()->add(
                'error',
                'The product '.$product->getProductName().' still has pending orders, please attend to them first and try again!'
            );
        } catch(\Doctrine\ORM\NoResultException $e) {

            $imagesDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/products/images';

            if ($product->getProductImage() != 'productDefault.jpeg') {
                $this->deleteFile($imagesDir.'/'.$product->getProductImage());
            }

            $product->setActive(false);

            // do not remove from database, things go wrong.
            // $em->remove($product);

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'The product '.$product->getProductName().' was successfully removed from your store!'
            );
        }

        return $this->redirect('/vendor/mystore');
    }


    /**
    * @Route("/vendor/shipcancel/{customerId}")
    */
    public function cancelShipmentAction($customerId) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            '
            SELECT o FROM AppBundle:Order o WHERE o.customerId=:customerId AND o.vendorId=:vendorId AND o.orderStatus=\'ACCEPTED BY VENDOR: Being Shipped\'
            '
        )->setParameter('customerId', $customerId)->setParameter('vendorId', $this->getUser()->getId());

        $orders = $query->getResult();

        foreach ($orders as $order) {
            $now = new \DateTime();
            $order->setTransactionDate($now);
            $order->setOrderStatus('CHECKED-OUT');
            $em->flush();
        }

        $this->get('session')->getFlashBag()->add(
            'notice',
            'The order was successfully moved to "Pending Orders".'
        );

        return $this->redirect('/shipments/view');
    }

    /**
    * @Route("/vendor/reject/{customerId}")
    */
    public function rejectOrderAction($customerId) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            '
            SELECT o FROM AppBundle:Order o WHERE o.customerId=:customerId AND o.vendorId=:vendorId AND o.orderStatus=\'CHECKED-OUT\'
            '
        )->setParameter('customerId', $customerId)->setParameter('vendorId', $this->getUser()->getId());

        $orders = $query->getResult();

        foreach ($orders as $order) {
            $now = new \DateTime();
            $order->setTransactionDate($now);
            $order->setOrderStatus('REJECTED');
            $em->flush();
        }

        $this->get('session')->getFlashBag()->add(
            'notice',
            'The order was successfully rejected.'
        );

        return $this->redirect('/vendor/view-pending');
    }

    /**
    * @Route("/vendor/delivery-guy-registration")
    */
    public function deliveryGuyRegistrationAction(Request $request)
    {
        $user = new User();
        $user->setMemberType("ROLE_DELIVERY_GUY");
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                -> encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setStoreId($this->getUser()->getStoreId());

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your delivery guy ' . $user->getFirstName() . ' was successfully registered!'
            );

            return $this->redirect('/vendor/mystore');
        }

        return $this->render(
            'Store/DeliveryGuyRegistration.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
    * @Route("/shipments/delivered/{customerId}")
    */
    public function deliveredAction($customerId) {
        $storeRepository = $this->getDoctrine()->getRepository("AppBundle:Store");
        $store = $storeRepository->findOneBystoreId($this->getUser()->getStoreId());

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            '
            SELECT o FROM AppBundle:Order o WHERE o.customerId=:customerId AND o.vendorId=:vendorId AND o.orderStatus=\'ACCEPTED BY VENDOR: Being Shipped\'
            '
        )->setParameter('customerId', $customerId)->setParameter('vendorId', $store->getVendorId());
        $orders = $query->getResult();

        foreach ($orders as $order) {
            $now = new \DateTime();
            $order->setTransactionDate($now);
            $order->setOrderStatus('DELIVERED');
            $order->setDeliveryGuy($this->getUser()->getId());
            $em->flush();
        }

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Order has been marked as successfully shipped, thank you!'
        );

        return $this->redirect('/shipments/view');
    }
}

?>

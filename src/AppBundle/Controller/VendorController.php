<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\ProductType;
use AppBundle\Form\UserType;
use AppBundle\Entity\Product;
use AppBundle\Entity\Member;
use AppBundle\Entity\DeliveryGuy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\FileSystem\Exception\IOExceptionInterface;

/**
* @Route("/vendor")
*/
class VendorController extends Controller {

    /**
    * @Route("/add-product")
    */
    public function addProductAction(Request $request) {
        $product = new Product();
        $product->setIsActive(true);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($product->getImage()) {
                $file = $product->getImage();
                $directory = $this->container->getParameter('kernel.root_dir').'/../web/uploads/products/images';
                $product->overwriteImage($directory, $file);
            } else {
                $product->setImage('productDefault.jpeg');
            }

            $product->setStore($this->getUser()->getStore());
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your product '. $product->getName() .' was successfully added to your store!'
            );

            return $this->redirect('/mystore');
        }

        return $this->render(
            'Store/AddProduct.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
    * @Route("/ship/{customer}")
    */
    public function shipAction(Member $customer) {
        $em = $this->getDoctrine()->getManager();

        $this->getUser()->getStore()->shipPendingOrders($customer);
        $em->flush();

        $notice = "All orders of ". $customer->getFullName() ." have been marked as SHIPPED successfully!";

        $this->get('session')->getFlashBag()->add(
            'notice',
            $notice
        );

        return $this->redirect('/mystore/view-pending');
    }

    /**
    * @Route("/edit/{productId}")
    */
    public function editProductAction($productId, Request $request) {
        $productRepository = $this->getDoctrine()->getRepository("AppBundle:Product");
        $oldProduct = $productRepository->findOneByid($productId);

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($product->getImage()) {
                $file = $product->getImage();
                $directory = $this->container->getParameter('kernel.root_dir').'/../web/uploads/products/images';
                $oldProduct->overwriteImage($directory, $file);
            }
            $oldProduct->setName($product->getName());
            $oldProduct->setQuantity($product->getQuantity());
            $oldProduct->setPrice($product->getPrice());
            $oldProduct->setDescription($product->getDescription());

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your product '. $product->getName() .' was successfully edited!'
            );

            return $this->redirect('/mystore');
        }

        return $this->render(
            'Store/EditProduct.html.twig',
            array(
                'form' => $form->createView(),
                'product' => $oldProduct
            )
        );
    }

    /**
    * @Route("/delete-product/{product}")
    */
    public function deleteProduct(Product $product) {
        $em = $this->getDoctrine()->getManager();

        $orderRepository = $this->getDoctrine()->getRepository("AppBundle:Order");
        $orders = $orderRepository->findAllPendingOrdersOfAProduct($product);

        if ($product->remove($orders)) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'The product '.$product->getName().' was successfully removed from your store!'
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                'The product '.$product->getName().' still has pending orders, please attend to them first and try again!'
            );
        }

        return $this->redirect('/mystore');
    }


    /**
    * @Route("/shipcancel/{customer}")
    */
    public function cancelShipmentAction(Member $customer) {
        $em = $this->getDoctrine()->getManager();
        $this->getUser()->getStore()->cancelShipment($customer);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'notice',
            'The orders of ' . $customer->getFullName() . ' were successfully moved to your "Pending Orders".'
        );
        return $this->redirect('/shipments/view');
    }

    /**
    * @Route("/reject/{customer}")
    */
    public function rejectOrderAction(Member $customer) {
        $em = $this->getDoctrine()->getManager();
        $this->getUser()->getStore()->rejectOrders($customer);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'The orders of ' . $customer->getFullName() . ' were successfully marked as REJECTED.'
        );

        return $this->redirect('/mystore/view-pending');
    }

    /**
    * @Route("/delivery-guy-registration")
    */
    public function deliveryGuyRegistrationAction(Request $request)
    {
        $user = new DeliveryGuy($this->getUser());
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                -> encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);

            $this->getUser()->addDeliveryGuy($user);

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your delivery guy ' . $user->getFirstName() . ' was successfully registered!'
            );
            return $this->redirect('/mystore');
        }

        return $this->render(
            'Store/DeliveryGuyRegistration.html.twig',
            array('form' => $form->createView())
        );
    }
}

?>

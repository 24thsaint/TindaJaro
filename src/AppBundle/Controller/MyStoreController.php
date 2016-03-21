<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Store;
use AppBundle\Entity\Member;
use AppBundle\Form\MemberType;
use AppBundle\Form\StoreDetailType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\FileSystem\Exception\IOExceptionInterface;


/**
* @Route("/mystore")
*/
class MyStoreController extends Controller {

    /**
    * @Route("/")
    */
    public function myStoreAction(Request $request) {
        $store = $this->getUser()->getStore();
        $productRepository = $this->getDoctrine()->getRepository("AppBundle:Product");
        $products = $productRepository->findAllActiveProductsInASpecificStore($store);

        $storeTmp = new Store();
        $form = $this->createForm(StoreDetailType::class, $storeTmp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $store->setName($storeTmp->getName());
            $store->setDescription($storeTmp->getDescription());
            $store->setMinimumPurchasePrice($storeTmp->getMinimumPurchasePrice());

            // handle image upload
            if ($storeTmp->getImage()) {
                $file = $storeTmp->getImage();
                $directory = $this->container->getParameter('kernel.root_dir').'/../web/uploads/stores/images';
                $store->overwriteImage($directory, $file);
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

            return $this->redirect('/mystore/');
        }

        return $this->render(
            'Homepage/MyStore.html.twig',
            array(
                'products' => $products,
                'store' => $store,
                'form' => $form->createView()
            )
        );
    }

    /**
    * @Route("/open")
    */
    public function storeOpenAction() {
        $em = $this->getDoctrine()->getManager();

        $this->getUser()->getStore()->open();
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Your store was successfully opened!'
        );
        return $this->redirect('/mystore');
    }

    /**
    * @Route("/close")
    */
    public function storeCloseAction() {
        $em = $this->getDoctrine()->getManager();

        $this->getUser()->getStore()->close();
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Your store was successfully closed!'
        );
        return $this->redirect('/mystore');
    }

    /**
    * @Route("/view-pending")
    */
    public function viewPendingAction() {

        $store = $this->getUser()->getStore();
        $pendingOrders = $store->getAllCheckedOutOrders();

        return $this->render(
            'Store/MyStorePending.html.twig',
            array(
                'pendingOrders' => $pendingOrders,
            )
        );
    }

    /**
    * @Route("/view-all")
    */
    public function viewShippedAction() {

        $store = $this->getUser()->getStore();
        $allOrders = $store->getAllOrders();

        return $this->render(
            'Store/MyStoreAllOrders.html.twig',
            array(
                'allOrders' => $allOrders
            )
        );
    }
}

?>

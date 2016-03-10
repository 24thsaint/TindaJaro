<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productName', TextType::class, array('label' => 'Product Name'))
            ->add('productQuantity', NumberType::class, array('label' => 'Product Quantity', 'scale' => 0))
            ->add('productPrice', MoneyType::class, array('label' => 'Price per Quantity', 'currency' => 'PHP', 'scale' => 2))
            ->add('productDescription', TextareaType::class, array('label' => 'Product Description'))
            ->add('productImage', FileType::class, array('label' => 'Product Image', 'required' => ''));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product'
        ));
    }
}

?>

<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class StoreDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('storename', TextType::class, array('label' => 'Store Name'))
            ->add('storedescription', TextareaType::class, array('label' => 'Store Description'))
            ->add('minimumpurchaseprice', MoneyType::class, array('label' => 'Minimum Purchase Price', 'currency' => 'PHP', 'scale' => 2))
            ->add('storeimage', FileType::class, array('label' => 'Store Image (leave blank if you want to maintain current store image)', 'required' => false));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Store'
        ));
    }
}

?>

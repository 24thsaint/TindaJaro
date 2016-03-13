<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class InfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array('label' => 'First Name'))
            ->add('lastname', TextType::class, array('label' => 'Last Name'))
            ->add('mobilenumber', NumberType::class, array('label' => 'Mobile Number'))
            ->add('homeaddress', TextType::class, array('label' => 'Home Address'))
            ->add('email', EmailType::class, array('label' => 'Email'))
            ->add('plainPassword', PasswordType::class, array('label' => 'Current Password'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'validation_groups' =>  array('userinfo')
        ));
    }

}

?>

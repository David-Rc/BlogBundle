<?php

namespace DavidUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'attr'=>array(
                    'class'=>'form-control'
                )
            ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password', 'attr'=>array(
                    'class'=>'form-control',
                    'type'=>'password'
                )),
                'second_options' => array('label' => 'Repeat Password', 'attr'=>array(
                    'class'=>'form-control',
                    'type'=>'password',
                )),

            ))
            ->add('email', EmailType::class, array(
                'attr'=>array(
                    'class'=>'form-control',
                    'type'=>'email',
                )
            ))
            ->add('avatar', FileType::class, array(
                'data_class'=>null,
                'required'=>false,
                'label'=>'Avatar  :'
            ))
            ->add('save', SubmitType::class, array(
                'attr'=>array(
                    'class'=>'btn btn-primary'
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DavidUserBundle\Entity\User'
        ));
    }
}

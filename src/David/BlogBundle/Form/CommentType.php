<?php

namespace David\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, array(
        'label' => false,
        'attr' => array(
            'class' => 'form-control',
            'rows'  => 3,
            'cols'  => 20,
            'placeholder'=>'Ecrivez un commentaire'
        )))
           ->add('save', SubmitType::class, array('label' => 'Valider',
               'attr' => array(
                   'class' => "btn btn-primary"
               )))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'David\BlogBundle\Entity\Comment'
        ));
    }
}

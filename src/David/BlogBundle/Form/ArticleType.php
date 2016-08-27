<?php

namespace David\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'attr'=>array(
                    'class'=>'form-control',
                    'type'=>'text'
                )
            ))
            ->add('content', TextareaType::class, array(
                'attr'=>array(
                    'class'=>'form-control',
                    'type'=>'textarea'
                )
            ))
            ->add('published', ChoiceType::class, array(
                'choices'=>array(
                    'yes'=>true,
                    'no'=>false,
                ),
            ))
            ->add('image', ImageType::class, array(
                'required'=>false,
                'label'=>false,
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
            'data_class' => 'David\BlogBundle\Entity\Article'
        ));
    }
}

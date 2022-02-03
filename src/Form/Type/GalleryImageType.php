<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Image;

class GalleryImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path',HiddenType::class)
            ->add('title', TextType::class, array('required' => FALSE, 'label' => 'image.title', 'translation_domain' => 'App'))
            ->add('alt', TextType::class, array('required' => FALSE, 'label' => 'image.alt', 'translation_domain' => 'App'))
            ->add('weight', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}

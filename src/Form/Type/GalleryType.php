<?php

namespace App\Form\Type;

use App\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label' => 'gallery.title', 'translation_domain' => 'App'))
            ->add('date', DateTimeType::class, array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'html5' => false, 'attr' => ['class' => 'datepicker'], 'label' => 'gallery.date', 'translation_domain' => 'App'))
            ->add('dateto', DateTimeType::class, array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'html5' => false, 'attr' => ['class' => 'datepicker'], 'label' => 'gallery.dateto', 'translation_domain' => 'App'))
            ->add('description', TextareaType::class, array('label' => 'gallery.description', 'translation_domain' => 'App'))
            ->add('addimages', SubmitType::class, array('label' => 'gallery.addimages', 'translation_domain' => 'App'))
            ->add('edit_images', SubmitType::class, array('label' => 'gallery.editimages', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'gallery.save', 'translation_domain' => 'App'));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
}

<?php

namespace App\Form\Type;

use App\Entity\HomeSlide;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Type\ImageType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomeslideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label' => 'homeslide.title', 'translation_domain' => 'App'))
            ->add('text', TextareaType::class, array('label' => 'homeslide.text', 'translation_domain' => 'App'))
            ->add('link_name', TextType::class, array('label' => 'homeslide.link_name', 'translation_domain' => 'App'))
            ->add('link_url', TextType::class, array('label' => 'homeslide.link_url', 'translation_domain' => 'App'))
            ->add('image', ImageType::class, array('label' => 'events.picture', 'translation_domain' => 'App'))
            ->add('video', TextType::class, array('label' => 'homeslide.video', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'homeslide.save', 'translation_domain' => 'App'));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HomeSlide::class,
        ]);
    }
}

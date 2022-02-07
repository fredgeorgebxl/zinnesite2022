<?php

namespace App\Form\Type;

use App\Entity\Textblock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class TextblockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'textblock.name', 'translation_domain' => 'App'))
            ->add('content', CKEditorType::class, array('label' => 'textblock.content', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'textblock.save', 'translation_domain' => 'App'));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Textblock::class,
        ]);
    }
}

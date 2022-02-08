<?php

namespace App\Form\Type;

use App\Entity\Parameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('type', ChoiceType::class, array('label' => 'parameter.type', 'translation_domain' => 'App', 'choices'  => $options['paramtypes']))
        ->add('name', TextType::class, array('label' => 'parameter.name', 'translation_domain' => 'App'))
        ->add('value', TextType::class, array('label' => 'parameter.value', 'translation_domain' => 'App'))
        ->add('save', SubmitType::class, array('label' => 'parameter.save', 'translation_domain' => 'App'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Parameter::class,
            'paramtypes' => [],
        ]);
    }
    
}

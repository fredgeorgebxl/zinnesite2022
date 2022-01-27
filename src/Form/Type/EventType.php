<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\Type\ImageType;

class EventType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        ->add('name', TextType::class, array('label' => 'events.name', 'translation_domain' => 'App'))
        ->add('date', DateTimeType::class, array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'html5' => false, 'attr' => ['class' => 'datepicker'], 'label' => 'events.date', 'translation_domain' => 'App'))
        ->add('description', CKEditorType::class, array('label' => 'events.description', 'translation_domain' => 'App'))
        ->add('location', TextType::class, array('label' => 'events.location', 'translation_domain' => 'App'))
        ->add('season', ChoiceType::class, array('choices'  => ['2021-2022' => '2021-2022'], 'label' => 'events.season', 'translation_domain' => 'App'))
        //->add('gallery', ChoiceType::class, array('choices'  => $this->galleries, 'label' => 'events.gallery', 'translation_domain' => 'App'))
        ->add('picture', ImageType::class, array('label' => 'events.picture', 'translation_domain' => 'App'))
        ->add('save', SubmitType::class, array('label' => 'events.save', 'translation_domain' => 'App'));
    }
}
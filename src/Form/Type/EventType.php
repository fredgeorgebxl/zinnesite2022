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
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Gallery;
use App\Entity\Parameter;

class EventType extends AbstractType{

    private $seasons;
    private $galleries;

    public function configureOptions(OptionsResolver $resolver) {
        //$resolver->setDefined('seasons-available');
        $resolver->setRequired('entity_manager');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $repositorygalleries = $options['entity_manager']->getRepository(Gallery::class);
        $qbg = $repositorygalleries->createQueryBuilder('g');
        $qbg->select('g.id', 'g.title')
           ->orderBy('g.title', 'ASC');
        $resultsg = $qbg->getQuery()->getResult();

        $repositoryseasons = $options['entity_manager']->getRepository(Parameter::class);
        $qbs = $repositoryseasons->createQueryBuilder('s');
        $qbs->select('s.name', 's.value')
            ->where('s.type = :parameter')
            ->orderBy('s.value', 'DESC')
            ->setParameter('parameter', 'seasons');
        $resultss = $qbs->getQuery()->getResult();
        
        $this->galleries = ['' => NULL];
        $this->seasons = [];
        
        foreach ($resultsg as $gallery){
            $this->galleries[$gallery['title']] = $gallery['id'];
        }

        foreach ($resultss as $season) {
            $this->seasons[$season['name']] = $season['value'];
        }

        $builder
        ->add('name', TextType::class, array('label' => 'events.name', 'translation_domain' => 'App'))
        ->add('date', DateTimeType::class, array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'html5' => false, 'attr' => ['class' => 'datepicker'], 'label' => 'events.date', 'translation_domain' => 'App'))
        ->add('description', CKEditorType::class, array('label' => 'events.description', 'translation_domain' => 'App'))
        ->add('location', TextType::class, array('label' => 'events.location', 'translation_domain' => 'App'))
        ->add('season', ChoiceType::class, array('choices'  => $this->seasons, 'label' => 'events.season', 'translation_domain' => 'App'))
        ->add('gallery', ChoiceType::class, array('choices'  => $this->galleries, 'label' => 'events.gallery', 'translation_domain' => 'App'))
        ->add('picture', ImageType::class, array('label' => 'events.picture', 'translation_domain' => 'App'))
        ->add('save', SubmitType::class, array('label' => 'events.save', 'translation_domain' => 'App'));
    }
}
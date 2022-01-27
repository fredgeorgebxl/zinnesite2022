<?php

namespace App\Form\Type;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $image = $event->getData();
            $form = $event->getForm();
            if($image && NULL != $image->getPath() && !empty($image->getPath())){
                $form->add('file', FileType::class, ['label' => 'image.change_file', 'mapped' => false, 'required' => false,'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'image.typeerror',
                    ])], 'translation_domain' => 'App']);
                $form->add('crop_coordinations', TextType::class, array('label' => 'image.crop', 'translation_domain' => 'App'));
                $form->add('remove_image', SubmitType::class, array('label' => 'image.remove', 'translation_domain' => 'App'));
            } else {
                $form->add('file', FileType::class, ['label' => 'image.file', 'mapped' => false, 'required' => false,'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'image.typeerror',
                    ])], 'translation_domain' => 'App']);
            }
        });
        $builder
            ->add('add_image', SubmitType::class, array('label' => 'image.add', 'translation_domain' => 'App'))
            ->add('title', TextType::class, array('label' => 'image.title', 'translation_domain' => 'App'))
            ->add('alt', TextType::class, array('label' => 'image.alt', 'translation_domain' => 'App'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
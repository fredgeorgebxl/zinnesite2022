<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Type\ImageType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserType extends AbstractType
{
    private $security;
    
    public function __construct(TokenStorageInterface $token) {
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->token->getToken()->getUser();

        $builder
            ->add('firstname', TextType::class, array('label' => 'users.firstname', 'translation_domain' => 'App'))
            ->add('lastname', TextType::class, array('label' => 'users.lastname', 'translation_domain' => 'App'))
            ->add('phone', TextType::class, array('label' => 'users.phone', 'translation_domain' => 'App'))
            ->add('email', EmailType::class, array('label' => 'users.email', 'translation_domain' => 'App'))
            ->add('password', PasswordType::class, array('label' => 'security.login.password', 'translation_domain' => 'App'))
            ->add('voice', ChoiceType::class, array('label' => 'users.voice.voice', 'translation_domain' => 'App', 
            'choices'  => array(
                'users.voice.soprane' => 'sopr',
                'users.voice.alto' => 'alto', 
                'users.voice.tenor' => 'teno',
                'users.voice.basse' => 'bass',
                'users.voice.chef' => 'chef'
            )))
            ->add('picture', ImageType::class, array('label' => 'users.picture', 'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'users.save', 'translation_domain' => 'App'))
        ;
        
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($user) {
                $form_user = $event->getData();
                $form = $event->getForm();
                if($form_user->getId()){
                    $form->remove('password');
                }
                if ($form_user->getId() !== $user->getId()){
                    $form->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'users.registred' => 'ROLE_USER',
                    'users.admin' => 'ROLE_ADMIN',
                    'users.superadmin' => 'ROLE_SUPER_ADMIN'
                ),
                'multiple' => true, 
                'expanded' => true, 
                'label' => 'users.roles', 
                'translation_domain' => 'App'));
                }
            });
            
    }
}

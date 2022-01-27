<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class ChangePasswordType extends AbstractType{

    private $token;
    
    public function __construct(TokenStorageInterface $token) {
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $user = $this->token->getToken()->getUser();

        $builder
            ->add('current_password', PasswordType::class, array('constraints' => [new NotBlank()], 'required' => TRUE, 'label' => 'security.current-password', 'translation_domain' => 'App'))
            ->add('new_password', RepeatedType::class, array('type' => PasswordType::class,
            'invalid_message' => 'security.password_match_error',
            'required' => true,
            'constraints' => [new NotBlank()],
            'first_options'  => ['label' => 'security.new-password'],
            'second_options' => ['label' => 'security.password-repeat'],
            'translation_domain' => 'App'))
            ->add('save', SubmitType::class, array('label' => 'general.confirm', 'translation_domain' => 'App'));

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($user, $options) {
                $form = $event->getForm();
                if (isset($options['attr']['user_id']) && !empty($options['attr']['user_id'])){
                    if($user->getId() != $options['attr']['user_id']){
                        $form->remove('current_password');
                    }
                }
            });
    }

}
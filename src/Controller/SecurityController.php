<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\Type\ChangePasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @Route("/admin/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/admin/change_password", name="app_change_password")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function changePassword(Request $request, TranslatorInterface $translator, UserPasswordHasherInterface $encoder)
    {
        $error = '';
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $factory = new PasswordHasherFactory([
                'common' => ['algorithm' => 'bcrypt'],
            ]);
            $passwordHasher = $factory->getPasswordHasher('common');
            $connected_user = $this->getUser();
            
            // If the current password is good
            if ($passwordHasher->verify($connected_user->getPassword(), $form->get('current_password')->getData())){
                // Load doctrine entity
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository(User::class)->find($connected_user->getId());
                
                if (!$user) {
                    throw $this->createNotFoundException(
                        'No user found for id '.$connected_user->getId()
                    );
                }
                $hash = $encoder->hashPassword($user, $form->get('new_password')->getData());
                $user->setPassword($hash);
                $em->flush();

                $this->addFlash('success', $translator->trans('security.password-changed', [], 'App'));
                return $this->redirectToRoute('admin_home');


            } else {
                // Send an error message
                $error = $translator->trans('security.current-password-wrong', [], 'App');
            }

        }
        

        return $this->render('security/change_password.html.twig', ['user' => false, 'error' => $error, 'form' => $form->createView()]);
    }

    /**
     * @Route("/admin/change_password/{user_id}", requirements={"user_id" = "\d+"}, name="app_change_password_user")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function changePasswordUser($user_id, Request $request, TranslatorInterface $translator, UserPasswordHasherInterface $encoder)
    {
        $error = '';
        $form = $this->createForm(ChangePasswordType::class, null, ['attr' => ['user_id' => $user_id]]);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user_id);
        
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$user_id
            );
        }

        if ($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->hashPassword($user, $form->get('new_password')->getData());
            $user->setPassword($hash);
            $em->flush();

            $this->addFlash('success', $translator->trans('security.password-changed', [], 'App'));
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('security/change_password.html.twig', ['user' => $user, 'error' => $error, 'form' => $form->createView()]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

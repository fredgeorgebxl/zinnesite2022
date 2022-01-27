<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\ImageManager;
use App\Entity\Image;

 /**
 * @Route("admin/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_list")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findBy(['active' => 1], ['firstname' => 'asc']);

        return $this->render('admin/user/index.html.twig', ['users' => $users, 'disabled' => false]);
    }

    /**
     * @Route("/disabled", name="disabled_user_list")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function indexDisabledAction()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findBy(['active' => 0], ['firstname' => 'asc']);

        return $this->render('admin/user/index.html.twig', ['users' => $users, 'disabled' => true]);
    }

    /**
     * @Route("/new", name="user_new")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function newAction(Request $request, UserPasswordHasherInterface $encoder, ImageManager $imageManager): Response
    {

       $user = new User();
       $user->setRoles(['ROLE_USER']);
       $form = $this->createForm(UserType::class,$user);
       $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid()){
            
            $user = $form->getData();
            $hash = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setActive(1);
            $em = $this->getDoctrine()->getManager();
            
            
            // Upload picture
            $file = $form->get('picture')->get('file')->getData();
            $alt = $form->get('picture')->get('alt')->getData();
            $title = $form->get('picture')->get('title')->getData();

            if($file){
                $picture = $imageManager->initPicture($file, $alt, $title);
                $user->setPicture($picture);
            } else {
                $user->setPicture(NULL);
            }

            $em->persist($user);
            $em->flush();

            if($form->get('picture')->has('add_image') && $form->get('picture')->get('add_image')->isClicked()){
                return $this->redirectToRoute('user_edit', ['user_id' => $user->getId()]);
            } else {
                return $this->redirectToRoute('user_list');
            }

        }

        return $this->render('admin/user/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/edit/{user_id}", requirements={"user_id" = "\d+"}, name="user_edit")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function editAction($user_id, Request $request, ImageManager $imageManager)
    {
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user_id);
        
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$user_id
            );
        }
        
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        $picture = $user->getPicture();
        $cropConfig = [];
        
        if ($picture && $picture->getHeight() > 0){
            $cropConfig = $imageManager->getFilterCroppingInfos($picture, 'site_user_square');
        }

        if($form->isSubmitted() && $form->isValid()){
            
            $crop_coordinations = false;
            // Get data from the 'file' field
            $file = $form->get('picture')->get('file')->getData();
            $alt = $form->get('picture')->get('alt')->getData();
            $title = $form->get('picture')->get('title')->getData();
            if ($form->has('crop_coordinations')){
                $crop_coordinations = $form->get('picture')->get('crop_coordinations')->getData();
            }
            if($file){
                // if there's no image, create a new one
                if(is_null($picture)){
                    $picture = $imageManager->initPicture($file, $alt, $title);
                    $cropConfig = $imageManager->getFilterCroppingInfos($picture, 'site_user_square');
                }
                // If there's an image, delete the current file...
                
                if(!empty($picture->getPath())){
                    $directory = $this->getParameter('images_directory_rel');
                    $imageManager->deletePictureFiles($directory, $picture);
                }
                // ...and upload the new file
                $imageManager->setFileToPicture($file, $picture);
            } else {
                // Update image associated fields
                $picture->setAlt($alt);
                $picture->setTitle($title);
                if ($crop_coordinations){
                    $picture->setCropCoordinations($crop_coordinations);
                }
            }
            
            // Remove image if necessary
            
            if($form->get('picture')->has('remove_image') && $form->get('picture')->get('remove_image')->isClicked()){
                $directory = $this->getParameter('images_directory_rel');
                $imageManager->deletePictureFiles($directory, $picture);
                $user->setPicture(NULL);
            }

            // Reset picture to NULL if the picture has no path to avoid an SQL error
            if(!is_null($picture) && empty($picture->getPath())){
                $user->setPicture(NULL);
            }

            $em->flush();
            
            if(($form->get('picture')->has('remove_image') && $form->get('picture')->get('remove_image')->isClicked()) || ($form->get('picture')->has('add_image') && $form->get('picture')->get('add_image')->isClicked())){
                return $this->redirectToRoute('user_edit', ['user_id' => $user_id]);
            } else {
                return $this->redirectToRoute('user_list');
            }
        }

        return $this->render('admin/user/edit.html.twig', array(
            'form' => $form->createView(),
            'picture' => $picture,
            'crop_config' => $cropConfig,
        ));
    }
    
    /**
     * @Route("/disable/{user_id}", requirements={"user_id" = "\d+"}, name="user_disable")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function disableAction($user_id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user_id);

        if (!in_array('ROLE_SUPER_ADMIN', $user->getRoles())){
            
            $user->setActive(false);
            
            $em->flush();
        }
        
        return $this->redirectToRoute('user_list');

    }
    
    /**
     * @Route("/enable/{user_id}", requirements={"user_id" = "\d+"}, name="user_enable")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function enableAction($user_id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user_id);
        
        if (!in_array('ROLE_SUPER_ADMIN', $user->getRoles())){
            
            $user->setActive(true);
            
            $em->flush();
        }
        
        return $this->redirectToRoute('disabled_user_list');
    }
    
    /**
     * @Route("/delete/{user_id}", requirements={"user_id" = "\d+"}, name="user_delete")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function deleteAction($user_id, Request $request, ImageManager $imageManager)
    {
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user_id);
        
        if (!in_array('ROLE_SUPER_ADMIN', $user->getRoles())){
   
            // Delete picture
            $picture = $user->getPicture();
            if (!is_null($picture)){
                $directory = $this->getParameter('images_directory_rel');
                $imageManager->deletePictureFiles($directory, $picture);
            }

            $em->remove($user);
            $em->flush();
        }
        
        return $this->redirectToRoute('disabled_user_list');
        
    }
}
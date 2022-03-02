<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\HomeSlide;
use App\Form\Type\HomeslideType;
use App\Service\ImageManager;
use App\Entity\Image;

/**
* @Route("admin/homeslide")
*/
class HomeSlideController extends AbstractController
{
    /**
     * @Route("/", name="homeslide_list")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(HomeSlide::class);
        $homeslides = $repository->findBy([], ['datecreated' => 'desc']);

        return $this->render('admin/home_slide/index.html.twig', [
            'controller_name' => 'HomeSlideController',
            'homeslides' => $homeslides,
        ]);
    }

    /**
     * @Route("/new", name="homeslide_new")
     * @IsGranted("ROLE_ADMIN")
     */
    public function newAction(Request $request, ImageManager $imageManager): Response
    {
        $homeslide = new HomeSlide();
        $homeslide->setSelected(0);

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(HomeslideType::class,$homeslide);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $homeslide = $form->getData();

            // Upload picture
            $file = $form->get('image')->get('file')->getData();
            $alt = $form->get('image')->get('alt')->getData();
            $title = $form->get('image')->get('title')->getData();

            if($file){
                $picture = $imageManager->initPicture($file, $alt, $title);
                $homeslide->setImage($picture);
            } else {
                $homeslide->setImage(NULL);
            }

            $em->persist($homeslide);
            $em->flush();
            
            if($form->get('image')->has('add_image') && $form->get('image')->get('add_image')->isClicked()){
                return $this->redirectToRoute('homeslide_edit', ['ent_id' => $homeslide->getId()]);
            } else {
                return $this->redirectToRoute('homeslide_list');
            }
        }

        return $this->render('admin/home_slide/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/edit/{ent_id}", requirements={"ent_id" = "\d+"}, name="homeslide_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAction($ent_id, Request $request, ImageManager $imageManager): Response
    {
        $em = $this->getDoctrine()->getManager();
        $homeslide = $em->getRepository(HomeSlide::class)->find($ent_id);
        
        if (!$homeslide) {
            throw $this->createNotFoundException(
                'No homeslide found for id '.$ent_id
            );
        }
        
        $form = $this->createForm(HomeslideType::class,$homeslide);
        $form->handleRequest($request);
        $picture = $homeslide->getImage();
        $cropConfig = [];
        
        if ($picture && $picture->getHeight() > 0){
            $cropConfig = $imageManager->getFilterCroppingInfos($picture, 'site_homepage_main');
        }

        if($form->isSubmitted() && $form->isValid()){
            
            $crop_coordinations = false;
            // Get data from the 'file' field
            $file = $form->get('image')->get('file')->getData();
            $alt = $form->get('image')->get('alt')->getData();
            $title = $form->get('image')->get('title')->getData();
            if ($form->has('crop_coordinations')){
                $crop_coordinations = $form->get('image')->get('crop_coordinations')->getData();
            }
            if($file){
                // if there's no image, create a new one
                if(is_null($picture)){
                    $picture = $imageManager->initPicture($file, $alt, $title);
                    $cropConfig = $imageManager->getFilterCroppingInfos($picture, 'site_homepage_main');
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
            
            if($form->get('image')->has('remove_image') && $form->get('image')->get('remove_image')->isClicked()){
                $directory = $this->getParameter('images_directory_rel');
                $imageManager->deletePictureFiles($directory, $picture);
                $homeslide->setImage(NULL);
            }

            // Reset picture to NULL if the picture has no path to avoid an SQL error
            if(!is_null($picture) && empty($picture->getPath())){
                $homeslide->setImage(NULL);
            }

            $em->flush();
            
            if(($form->get('image')->has('remove_image') && $form->get('image')->get('remove_image')->isClicked()) || ($form->get('image')->has('add_image') && $form->get('image')->get('add_image')->isClicked())){
                return $this->redirectToRoute('homeslide_edit', ['ent_id' => $ent_id]);
            } else {
                return $this->redirectToRoute('homeslide_list');
            }
        }

        return $this->render('admin/home_slide/edit.html.twig', array(
            'form' => $form->createView(),
            'picture' => $picture,
            'crop_config' => $cropConfig,
        ));
    }

     /**
     * @Route("/delete/{ent_id}", requirements={"ent_id" = "\d+"}, name="homeslide_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction($ent_id, ImageManager $imageManager): Response
    {
        $em = $this->getDoctrine()->getManager();
        $homeslide = $em->getRepository(HomeSlide::class)->find($ent_id);
        
         // Delete picture
        $picture = $homeslide->getImage();

        if (!is_null($picture)){
            $directory = $this->getParameter('images_directory_rel');
            $imageManager->deletePictureFiles($directory, $picture);
        }
        
        $em->remove($homeslide);
        $em->flush();
        
        return $this->redirectToRoute('homeslide_list');
    }

    /**
     * @Route("/select/{ent_id}", requirements={"ent_id" = "\d+"}, name="homeslide_select")
     * @IsGranted("ROLE_ADMIN")
     */
    public function selectAction($ent_id): Response
    {
        // Set selected to 0 for all entries
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository(HomeSlide::class);
        $qb = $rep->createQueryBuilder('h');
        $qb->update()
        ->set('h.selected', 0);
        $query = $qb->getQuery();
        $result = $query->getResult();

        // Set selected to 1 for the selected homeslide
        $homeslide = $rep->find($ent_id);
        if (!$homeslide) {
            throw $this->createNotFoundException(
                'No event found for id '.$ent_id
            );
        }
        $homeslide->setSelected(1);
        $em->flush();

        return $this->redirectToRoute('homeslide_list');
    }
}

<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Event;
use App\Form\Type\EventType;
use App\Service\ImageManager;
use App\Entity\Image;

/**
 * @Route("admin/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="event_list")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Event::class);
        $events = $repository->findBy([], ['date' => 'desc']);

        return $this->render('admin/event/index.html.twig', [
            'controller_name' => 'EventController',
            'events' => $events,
        ]);
    }

    /**
     * @Route("/new", name="event_new")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function newAction(Request $request, ImageManager $imageManager)
    {
        $event = new Event();
        $event->setPublished(TRUE);

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(EventType::class,$event, ['entity_manager' => $em]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $event = $form->getData();

            // Upload picture
            $file = $form->get('picture')->get('file')->getData();
            $alt = $form->get('picture')->get('alt')->getData();
            $title = $form->get('picture')->get('title')->getData();

            if($file){
                $picture = $imageManager->initPicture($file, $alt, $title);
                $event->setPicture($picture);
            } else {
                $event->setPicture(NULL);
            }

            $em->persist($event);
            $em->flush();
            
            if($form->get('picture')->has('add_image') && $form->get('picture')->get('add_image')->isClicked()){
                return $this->redirectToRoute('event_edit', ['ent_id' => $event->getId()]);
            } else {
                return $this->redirectToRoute('event_list');
            }
        }

        return $this->render('admin/event/new.html.twig', ['form' => $form->createView()]);
    }
    
    /**
     * @Route("/edit/{ent_id}", requirements={"ent_id" = "\d+"}, name="event_edit")
     */
    public function editAction($ent_id, Request $request, ImageManager $imageManager){
        
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($ent_id);
        
        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for id '.$ent_id
            );
        }
        /*
        $zinneparams = $this->getParameter('zinne');
        */
        $form = $this->createForm(EventType::class,$event, ['entity_manager' => $em]);
        $form->handleRequest($request);
        $picture = $event->getPicture();
        
        if($form->isSubmitted() && $form->isValid()){
            // Get data from the 'file' field
            $file = $form->get('picture')->get('file')->getData();
            $alt = $form->get('picture')->get('alt')->getData();
            $title = $form->get('picture')->get('title')->getData();
        
            
            if($file){
                // if there's no image, create a new one
                if(is_null($picture)){
                    $picture = $imageManager->initPicture($file, $alt, $title);
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
            }
            
            // Remove image if necessary
            if($form->get('picture')->has('remove_image') && $form->get('picture')->get('remove_image')->isClicked()){
                $directory = $this->getParameter('images_directory_rel');
                $imageManager->deletePictureFiles($directory, $picture);
                $event->setPicture(NULL);
            }

            // Reset picture to NULL if the picture has no path to avoid an SQL error
            if(!is_null($picture) && empty($picture->getPath())){
                $event->setPicture(NULL);
            }
            
            $em->flush();

            if($form->get('picture')->has('add_image') && $form->get('picture')->get('add_image')->isClicked()){
                return $this->redirectToRoute('event_edit', ['ent_id' => $event->getId()]);
            } else {
                return $this->redirectToRoute('event_list');
            }
        }
        
        return $this->render('admin/event/edit.html.twig', array(
            'form' => $form->createView(),
            'picture' => $picture,
        ));
        
    }
    
    /**
     * @Route("/delete/{ent_id}", requirements={"ent_id" = "\d+"}, name="event_delete")
     */
    public function deleteAction($ent_id, ImageManager $imageManager){

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($ent_id);
        
         // Delete picture
        $picture = $event->getPicture();

        if (!is_null($picture)){
            $directory = $this->getParameter('images_directory_rel');
            $imageManager->deletePictureFiles($directory, $picture);
        }
        
        $em->remove($event);
        $em->flush();
        
        return $this->redirectToRoute('event_list');

    }
    
}

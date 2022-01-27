<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use App\Repository\VideoRepository;
use App\Form\Type\VideoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("admin/video")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/", name="video_list")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Video::class);
        $videos = $repository->findBy([], ['title' => 'asc']);

        return $this->render('admin/video/index.html.twig', [
            'videos' => $videos,
        ]);
    }

     /**
     * @Route("/new", name="video_new")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function newAction(Request $request)
    {
        
       $video = new Video();
       $video->setPublished(TRUE);

       $form = $this->createForm(VideoType::class,$video);
       $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $video = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();
            
            return $this->redirectToRoute('video_list');
        }

        return $this->render('admin/video/new.html.twig', ['form' => $form->createView()]);

    }
    
    /**
     * @Route("/edit/{ent_id}", requirements={"ent_id" = "\d+"}, name="video_edit")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAction($ent_id, Request $request){
       
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository(Video::class)->find($ent_id);
        
        if (!$video) {
            throw $this->createNotFoundException(
                'No video found for id '.$ent_id
            );
        }
         
        $form = $this->createForm(VideoType::class,$video);
        $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid()){

            $em->flush();
            
            return $this->redirectToRoute('video_list');
        }
        
        return $this->render('admin/video/edit.html.twig', array(
            'form' => $form->createView(),
        ));

    }

     /**
     * @Route("/delete/{ent_id}", requirements={"ent_id" = "\d+"}, name="video_delete")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction($ent_id){

        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository(Video::class)->find($ent_id);
        $em->remove($video);
        $em->flush();
        
        return $this->redirectToRoute('video_list');

    }
}

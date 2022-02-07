<?php

namespace App\Controller\Admin;

use App\Entity\Textblock;
use App\Repository\TextblockRepository;
use App\Form\Type\TextblockType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("admin/textblock")
 */
class TextblockController extends AbstractController
{
    /**
     * @Route("/", name="textblock_list")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Textblock::class);
        $textblocks = $repository->findBy([], ['datecreated' => 'desc']);

        return $this->render('admin/textblock/index.html.twig', [
            'textblocks' => $textblocks,
        ]);
    }

    /**
     * @Route("/new", name="textblock_new")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function newAction(Request $request): Response
    {
        $textblock = new Textblock();
        $textblock->setPublished(TRUE);

        $form = $this->createForm(TextblockType::class,$textblock);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $textblock = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($textblock);
            $em->flush();
            
            return $this->redirectToRoute('textblock_list');
        }

        return $this->render('admin/textblock/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/edit/{ent_id}", requirements={"ent_id" = "\d+"}, name="textblock_edit")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAction($ent_id, Request $request){

        $em = $this->getDoctrine()->getManager();
        $textblock = $em->getRepository(Textblock::class)->find($ent_id);
        
        if (!$textblock) {
            throw $this->createNotFoundException(
                'No repertoire found for id '.$ent_id
            );
        }
        
        $form = $this->createForm(TextblockType::class,$textblock);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return $this->redirectToRoute('textblock_list');
        }
        
        return $this->render('admin/textblock/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/delete/{ent_id}", requirements={"ent_id" = "\d+"}, name="textblock_delete")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction($ent_id){

        $em = $this->getDoctrine()->getManager();
        $textblock = $em->getRepository(Textblock::class)->find($ent_id);
        $em->remove($textblock);
        $em->flush();
        
        return $this->redirectToRoute('textblock_list');
    }
}

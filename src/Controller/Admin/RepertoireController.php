<?php

namespace App\Controller\Admin;

use App\Entity\Repertoire;
use App\Repository\RepertoireRepository;
use App\Form\Type\RepertoireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\ImageManager;
use App\Entity\Image;

 /**
 * @Route("admin/repertoire")
 */
class RepertoireController extends AbstractController
{
    /**
     * @Route("/", name="repertoire_list")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Repertoire::class);
        $repertoires = $repository->findBy([], ['title' => 'asc']);

        return $this->render('admin/repertoire/index.html.twig', ['repertoires' => $repertoires]);
    }

    /**
     * @Route("/new", name="repertoire_new")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function newAction(Request $request)
    {

       $repertoire = new Repertoire();
       $repertoire->setPublished(TRUE);

       $form = $this->createForm(RepertoireType::class,$repertoire);
       $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $repertoire = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($repertoire);
            $em->flush();
            
            return $this->redirectToRoute('repertoire_list');
        }

        return $this->render('admin/repertoire/new.html.twig', ['form' => $form->createView()]);
    }
    
    /**
     * @Route("/edit/{ent_id}", requirements={"ent_id" = "\d+"}, name="repertoire_edit")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAction($ent_id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $repertoire = $em->getRepository(Repertoire::class)->find($ent_id);
        
        if (!$repertoire) {
            throw $this->createNotFoundException(
                'No repertoire found for id '.$ent_id
            );
        }
        
        $form = $this->createForm(RepertoireType::class,$repertoire);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $em->flush();
            
            return $this->redirectToRoute('repertoire_list');
        }
        
        return $this->render('admin/repertoire/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

     /**
     * @Route("/delete/{ent_id}", requirements={"ent_id" = "\d+"}, name="repertoire_delete")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction($ent_id){
        $em = $this->getDoctrine()->getManager();
        $repertoire = $em->getRepository(Repertoire::class)->find($ent_id);
        $em->remove($repertoire);
        $em->flush();
        
        return $this->redirectToRoute('repertoire_list');
    }
}

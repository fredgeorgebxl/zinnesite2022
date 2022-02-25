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
    public function editAction(Request $request, ImageManager $imageManager): Response
    {

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

<?php

namespace App\Controller\Admin;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use App\Form\Type\ParameterType;
use App\Form\Type\ParameterListType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("admin/parameter")
 */
class ParameterController extends AbstractController
{
    /**
     * @Route("/", name="parameter_list")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function index(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Parameter::class);
        $parameters = $repository->findBy([], ['name' => 'asc']);

        $default_values = array('parameters' => $parameters);
        $form = $this->createForm(ParameterListType::class,$default_values);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/parameter/index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/new", name="parameter_new")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function newAction(Request $request): Response
    {
        $types = $this->getParameter('parameter_types');
        $paramtypes = [];
        foreach ($types as $type){
            $paramtypes[$type] = $type;
        }

        $parameter = new Parameter();

        $form = $this->createForm(ParameterType::class,$parameter, ['paramtypes' => $paramtypes]);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $parameter = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($parameter);
            $em->flush();
            
            return $this->redirectToRoute('parameter_list');
        }

        return $this->render('admin/parameter/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{ent_id}", requirements={"ent_id" = "\d+"}, name="parameter_delete")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function deleteAction($ent_id){
        $em = $this->getDoctrine()->getManager();
        $parameter = $em->getRepository(Parameter::class)->find($ent_id);
        $em->remove($parameter);
        $em->flush();
        
        return $this->redirectToRoute('parameter_list');
    }
}

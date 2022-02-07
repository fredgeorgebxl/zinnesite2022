<?php

namespace App\Controller\Admin;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Parameter::class);
        $parameters = $repository->findBy([], ['name' => 'asc']);

        return $this->render('admin/parameter/index.html.twig', ['parameters' => $parameters]);
    }

    /**
     * @Route("/new", name="parameter_new")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function newAction(): Response
    {
        
    }
}

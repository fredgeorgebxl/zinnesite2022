<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\HomeSlide;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $homeslide = [];
        $homeslide = $em->getRepository(HomeSlide::class)->findOneBy(['selected' => 1]);
        if ($homeslide->getImage()){
            $path = $homeslide->getImage()->getPath();
        }

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'homeslide' => $homeslide,
        ]);
    }
}

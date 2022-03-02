<?php

namespace App\Controller\Admin;

use App\Entity\Repertoire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\HomeSlide;

/**
 * @Route("/admin")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="admin_home")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $homeslide = [];
        $homeslide = $em->getRepository(HomeSlide::class)->findOneBy(['selected' => 1]);
        if ($homeslide->getImage()){
            $path = $homeslide->getImage()->getPath();
        }
        
        return $this->render('admin/default/index.html.twig', ['homeslide' => $homeslide]);
    }
    
    /**
     * @Route("/clearcache", name="clear_cache")
     */
    /*
    public function clearCache($env = 'prod', $debug = true)
    {
        $kernel = new \AppKernel($env, $debug);
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'cache:clear'
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);
        
        $this->addFlash("success", $output->fetch());
        
        return $this->redirectToRoute('admin_home');
    }
    */
    
    /**
     * @Route("/switchpublish/{entity}/{ent_id}", requirements={"ent_id" = "\d+"}, name="switchpublish")
     */
    public function switchPublish($entity, $ent_id)
    {
        $class = "\App\Entity\\". \ucfirst($entity);
        
        $authorized_classes = $this->getParameter('publishable_entities');
        
        if(! in_array($entity, $authorized_classes)){
            throw $this->createNotFoundException(
                    "The class " . $entity . " is not allowed to be publishable (view in config.yml)"
            );
        }
        $em = $this->getDoctrine()->getManager();
        try {
            $rep = $em->getRepository($class);
        } catch (Doctrine\Common\Persistence\Mapping\MappingException $e) {
                
        }
        
        $myentity = $rep->find($ent_id);
        
        if (!$myentity) {
            throw $this->createNotFoundException(
                'No entity found for id '.$ent_id
            );
        }
        
        $myentity->switchPublish();
        
        $em->flush();
        
        return $this->redirectToRoute($entity . '_list');
    }
}

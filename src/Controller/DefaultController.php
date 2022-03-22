<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\HomeSlide;
use App\Entity\Event;
use App\Entity\Image;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        // Get selected home slide
        $em = $this->getDoctrine()->getManager();
        $homeslide = [];
        $homeslide = $em->getRepository(HomeSlide::class)->findOneBy(['selected' => 1]);
        if ($homeslide->getImage()){
            $path = $homeslide->getImage()->getPath();
        }

        // Get next concerts
        $nextconcerts = [];
        $repo = $em->getRepository(Event::class);
        $queryEvents = $repo->createQueryBuilder('ev')
                ->where('ev.published = 1')
                ->andWhere('ev.date >= :datenow')
                ->setParameter('datenow', date('Y-m-d H:i:s'))
                ->orderBy('ev.date', 'ASC')
                ->getQuery();
        $nextconcerts = $queryEvents->getResult();

        // get random pictures from galleries
        $photos_rep = $em->getRepository(Image::class);
        $queryImages = $photos_rep->createQueryBuilder('p');
        $queryImages->select('p.id')->where($queryImages->expr()->isNotNull('p.gallery'));

        $pictures_id = $queryImages->getQuery()->getArrayResult();
        $selected = [];
        $query_array = [];
        if (count($pictures_id)){
            $selected = array_rand($pictures_id, 6);
        }
        foreach ($selected as $cid){
            $query_array[] = $pictures_id[$cid]["id"];
        }
        $pictures = $photos_rep->createQueryBuilder('p')
                ->where('p.id IN (:ids)')
                ->setParameter('ids', $query_array)
                ->getQuery()
                ->getResult();

        $response = new Response(
            $this->render('default/index.html.twig', [
                'controller_name' => 'DefaultController',
                'homeslide' => $homeslide,
                'nextconcerts' => $nextconcerts,
                'pictures' => $pictures,
            ])
        );
        $response->setSharedMaxAge(3600);

        return $response;
    }

    /**
     * @Route("/agenda", name="agenda")
     */
    public function agendaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(\App\Entity\Event::class);
        $queryEvents = $repo->createQueryBuilder('ev')
                ->where('ev.published = 1')
                ->andWhere('ev.date >= :datenow')
                ->setParameter('datenow', date('Y-m-d H:i:s'))
                ->orderBy('ev.date', 'ASC')
                ->getQuery();
        $pastEventsQuery = $repo->createQueryBuilder('ev')
                ->where('ev.published = 1')
                ->andWhere('ev.date < :datenow')
                ->setParameter('datenow', date('Y-m-d H:i:s'))
                ->orderBy('ev.season', 'DESC')
                ->orderBy('ev.date', 'DESC')
                ->getQuery();
        $events = $queryEvents->getResult();
        $pastevents = $pastEventsQuery->getResult();
        
        return $this->render('default/agenda.html.twig', ['events' => $events, 'pastevents' => $pastevents]);
    }

    /**
     * @Route("/agenda/{slug}", name="event")
     */
    public function eventAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(\App\Entity\Event::class)->findOneBy([ 'slug' => $slug, 'published' => 1]);
        
        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for slug '.$slug
            );
        }
        $gal_id = $event->getGallery();
        if ($gal_id != 0){
            $gallery = $em->getRepository(\App\Entity\Gallery::class)->findOneBy(['id' => $gal_id]);
        } else {
            $gallery = NULL;
        }
        return $this->render('default/event.html.twig', ['event' => $event, 'gallery' => $gallery]);
    }

    /**
     * @Route("/repertoire", name="repertoire")
     */
    public function repertoireAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repertoire = $em->getRepository(\App\Entity\Repertoire::class)->findBy(['published' => 1, 'active' => 1], ['title' => 'asc']);
        $oldrepertoire = $em->getRepository(\App\Entity\Repertoire::class)->findBy(['published' => 1, 'active' => 0], ['title' => 'asc']);
        
        return $this->render('default/repertoire.html.twig', ['repertoire' => $repertoire, 'oldrepertoire' => $oldrepertoire]);
    }

    /**
     * @Route("/membres", name="membres")
     */
    public function membresAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(\App\Entity\User::class);
        $queryMembers = $repo->createQueryBuilder('m')
                ->where('m.active = 1')
                ->andWhere('m.voice != \'chef\'')
                ->orderBy('m.firstname', 'ASC')
                ->getQuery();
        $members = $queryMembers->getResult();
        $chef = $em->getRepository(\App\Entity\User::class)->findOneBy([ 'voice' => 'chef', 'active' => 1]);
        
        return $this->render('default/membres.html.twig', ['chef' => $chef, 'members' => $members]);
    }

    /**
     * @Route("/photos", name="photos")
     */
    public function photosAction()
    {        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(\App\Entity\Gallery::class);
        $qb = $repo->createQueryBuilder('gal');
        $queryEvents = $qb
                ->where('gal.published = 1')
                ->orderBy('gal.date', 'DESC')
                ->getQuery();
        $galleries = $queryEvents->getResult();
        
        return $this->render('default/photos.html.twig', ['galleries' => $galleries]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(\App\Form\Type\ContactType::class);
        $form->handleRequest($request);
        $messagesent = NULL;
        
        if($form->isSubmitted() &&  $form->isValid()){
            $name = $form['name']->getData();
            $email = $form['information']->getData();
            $subject = $form['subject']->getData();
            $message = $form['message']->getData();
            $honeypot = $form['email']->getData();
            
            if ($honeypot->trim()->isEmpty()){
                $email = (new Email())
               ->from($email)
               ->subject($subject)
               ->html($this->renderView('mails/contactmail.html.twig',array('name' => $name, 'email' => $email, 'message' => $message)),'text/html');
            
                $messagesent = TRUE;
                try {
                    $mailer->send($email);
                }  catch (TransportExceptionInterface $e) {
                    $messagesent = FALSE;
                }
            }
        }
        
        return $this->render('default/contact.html.twig', ['form' => $form->createView(), 'messagesent' => $messagesent]);
    }

    /**
     * @Route("/nous-rejoindre", name="joinus")
     */
    public function joinusAction()
    {
        return $this->render('default/joinus.html.twig');
    }

    /**
     * @Route("sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     * @param Request $request
     * @return Response
     */
    public function sitemapAction(Request $request)
    {
        $hostname = $request->getSchemeAndHttpHost();
        $urls = [];
        $urls[] = ['loc' => $this->generateUrl('homepage')];
        $response = new Response(
            $this->renderView('sitemap/index.xml.twig', [
                    'urls' => $urls,
                    'hostname' => $hostname]
            )
        );
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
}
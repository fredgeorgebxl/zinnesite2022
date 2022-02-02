<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Gallery;
use App\Form\Type\GalleryType;
use App\Service\ImageManager;
use App\Entity\Image;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("admin/gallery")
 */
class GalleryController extends AbstractController
{
    /**
     * @Route("/", name="gallery_list")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Gallery::class);
        $galleries = $repository->findBy([], ['date' => 'desc']);

        return $this->render('admin/gallery/index.html.twig', [
            'controller_name' => 'GalleryController',
            'galleries' => $galleries,
        ]);
    }

    /**
     * @Route("/new", name="gallery_new")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function newAction(Request $request)
    {
       $gallery = new Gallery();
       $gallery->setPublished(TRUE);

       $form = $this->createForm(GalleryType::class,$gallery);
       $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $gallery = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($gallery);
            $em->flush();
            
            if($form->get('addimages')->isClicked()){
                return $this->redirectToRoute('gallery_addimages', ['ent_id' => $gallery->getId()]);
            } else {
                return $this->redirectToRoute('gallery_list');
            }
        }

        return $this->render('admin/gallery/new.html.twig', ['form' => $form->createView()]);
    }
    
    /**
     * @Route("/edit/{ent_id}", requirements={"ent_id" = "\d+"}, name="gallery_edit")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAction($ent_id, Request $request){

        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($ent_id);
        
        if (!$gallery) {
            throw $this->createNotFoundException(
                'No video found for id '.$ent_id
            );
        }
         
        $form = $this->createForm(GalleryType::class,$gallery);
        $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();

            if($form->get('addimages')->isClicked()){
                return $this->redirectToRoute('gallery_addimages', ['ent_id' => $ent_id]);
            } elseif ($form->get('edit_images')->isClicked()) {
                return $this->redirectToRoute('gallery_editimages', ['ent_id' => $ent_id]);
            } else {
                return $this->redirectToRoute('gallery_list');
            }
        }
        
        return $this->render('admin/gallery/edit.html.twig', array(
            'form' => $form->createView(),
            'images' => $gallery->getPictures(),
        ));
    }

    /**
     * @Route("/addimages/{ent_id}", requirements={"ent_id" = "\d+"}, name="gallery_addimages")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addfileAction($ent_id, Request $request, ImageManager $imageManager){
        
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($ent_id);
        
        if (!$gallery) {
            throw $this->createNotFoundException(
                'No gallery found for id '.$ent_id
            );
        }
        
        $builder = $this->createFormBuilder();
            $builder->setAction($this->generateUrl('gallery_addimages', ['ent_id' => $ent_id]))
                ->add('file', FileType::class, ['label' => 'image.file', 'mapped' => false, 'required' => false,'constraints' => [
                new File([
                    'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'image.typeerror',
                ])], 'translation_domain' => 'App']);
            
            $builder->add('edit_gallery', SubmitType::class, array('label' => 'gallery.editgallery', 'translation_domain' => 'App'));
            $builder->add('edit_images', SubmitType::class, array('label' => 'gallery.editimages', 'translation_domain' => 'App'));
            $form = $builder->getForm();
        
        $form->handleRequest($request);
 
        if($form->isSubmitted() && $form->isValid()){
            
            $em = $this->getDoctrine()->getManager();
            
            // Upload picture

            $file = $request->files->get('file');

            if($file){
                $picture = $imageManager->initPicture(null, null, null);
                $picture->setGallery($gallery);
                try{
                    $imageManager->setFileToPicture($file, $picture);
                }catch(Exception $e){
                    return new JsonResponse(['error' => $e->getMessage()]);
                }
                $em->persist($picture);
                $em->flush();
                return new JsonResponse(['success' => true]);
            }

            if($form->has('edit_gallery') && $form->get('edit_gallery')->isClicked()){
                return $this->redirectToRoute('gallery_edit', ['ent_id' => $ent_id]);
            }
            if($form->get('edit_images')->isClicked()){
                return $this->redirectToRoute('gallery_editimages', ['ent_id' => $ent_id]);
            }
        }
        
        return $this->render('admin/gallery/addimages.html.twig', array(
            'form' => $form->createView(),
            'gallery' => $gallery,
        ));
    }
    
    /**
     * @Route("/editimages/{ent_id}", requirements={"ent_id" = "\d+"}, name="gallery_editimages")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editimagesAction($ent_id, Request $request){
        /*
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($ent_id);
        
        if (!$gallery) {
            throw $this->createNotFoundException(
                'No gallery found for id '.$ent_id
            );
        }
        $form = $this->createFormBuilder($gallery)
                ->add('title', HiddenType::class)
                ->add('pictures', CollectionType::class, array('error_bubbling' => FALSE, 'allow_delete' => true, 'entry_type' => GalleryImageType::class, 'entry_options' => array('attr' => array('class' => 'image-box'))))
                ->add('save', SubmitType::class, array('label' => 'gallery.save', 'translation_domain' => 'App'))
                ->getForm();
        
        $originalImages = new ArrayCollection();
        
        foreach ($gallery->getPictures() as $image) {
            $originalImages->add($image);
        }
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            foreach ($originalImages as $image) {
                if (false === $gallery->getPictures()->contains($image)) {
                    $gallery->removePicture($image);
                    $this->get('responsive_image')->deleteImageFiles($image, TRUE, TRUE);
                    $em->remove($image);
                }
            }
            
            $em->persist($gallery);
            $em->flush();
            
            if ($gallery->getHomeslide()){
                return $this->redirectToRoute('admin_home');
            } else {
                return $this->redirectToRoute('gallery_edit', ['ent_id' => $ent_id]);
            }
        }
        
        return $this->render('admin/gallery/editimages.html.twig', array(
            'form' => $form->createView(),
            'homeslide' => $gallery->getHomeslide(),
            'ent_id' => $ent_id,
        ));
        */
    }
    
    /**
     * @Route("/cropimage/{ent_id}/{img_id}", requirements={"ent_id" = "\d+", "img_id" = "\d+"}, name="gallery_cropimage")
     * @IsGranted("ROLE_ADMIN")
     */
    public function cropimageAction($ent_id, $img_id, Request $request){
        /*
        $em = $this->getDoctrine()->getManager();
        $image = $em->getRepository(ResponsiveImage::class)->find($img_id);
        
        if (!$image) {
            throw $this->createNotFoundException(
                'No image found for id '.$img_id
            );
        }
        $form = $this->createFormBuilder($image)
                ->add('crop_coordinates', \IrishDan\ResponsiveImageBundle\Form\CropFocusType::class, array('data' => $image, 'label' => 'image.crop_focus', 'translation_domain' => 'App'))
                ->add('save', SubmitType::class, array('label' => 'image.save', 'translation_domain' => 'App'))
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            $this->get('responsive_image')->deleteImageFiles($image, FALSE, TRUE);
            
            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('gallery_editimages', ['ent_id' => $ent_id]);
            
        }
        
        return $this->render('admin/gallery/cropimage.html.twig', array(
            'form' => $form->createView(),
        ));
        */
    }

     /**
     * @Route("/delete/{ent_id}", requirements={"ent_id" = "\d+"}, name="gallery_delete")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction($ent_id){

        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($ent_id);
        $em->remove($gallery);
        $em->flush();
        
        return $this->redirectToRoute('gallery_list');

    }
}

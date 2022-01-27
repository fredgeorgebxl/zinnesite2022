<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Form\Type\ImageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ImageController extends AbstractController
{
    /**
     * @Route("admin/image/new", name="image_new")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, FileUploader $fileUploader)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            if ($file) {
                $imageFile = $fileUploader->upload($file);
                $image->setPath($imageFile['name']);
            }

            // ... persist the $product variable or any other work

            return $this->redirectToRoute('image_new');
        }

        return $this->render('admin/image/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

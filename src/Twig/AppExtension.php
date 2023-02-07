<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ImageManager;


class AppExtension extends AbstractExtension
{
    private $em;

    public function __construct(EntityManagerInterface $em,  ImageManager $imageManager)
    {
        $this->em = $em;
        $this->imageManager = $imageManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('cropped_image', [$this, 'generateCroppedImage'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new TwigFunction('embed_video', [$this, 'embedYoutubeVideo'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new TwigFunction('text_block', [$this, 'insertTextBlock'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new TwigFunction('style_is_cropped', [$this, 'styleIsCropped'], [
                'is_safe' => ['html'],
                'needs_environment' => false,
            ]
        ),
        ];
    }


    public function generateCroppedImage(Environment $environment, $picture, $style)
    {
        return $environment->render('default/img.html.twig', [
            'picture' => $picture,
            'style' => $style,
        ]);
    }
    
    public function embedYoutubeVideo(Environment $environment, $url){
        // Get the Youtube ID from the URL
        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/", $url, $matches);
        $youtube_id = $matches[1];
        
        return $environment->render('default/video.html.twig', [
           'video_id' => $youtube_id, 
        ]);
    }
    
    public function insertTextBlock(Environment $environment, $blockname, $truncated = false){
        //$em = $this->doctrine->getManager();
        $textBlock = $this->em->getRepository(\App\Entity\Textblock::class)->findOneByName($blockname);
        return $environment->render('default/textblock.html.twig', [
            'textblock' => $textBlock,
            'truncated' => $truncated,
        ]);
    }

    public function styleIsCropped($style){
        return $this->imageManager->filterIsCropped($style);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}


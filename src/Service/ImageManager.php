<?php

/*
 * This file is part of the `liip/LiipImagineBundle` project.
 *
 * (c) https://github.com/liip/LiipImagineBundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Service;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Service\FilterPathContainer;
use App\Entity\Image;
use App\Service\FileUploader;
use Symfony\Component\Filesystem\Exception\ExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageManager
{
    /**
     * @var CacheManager
     */
    private $cacheManager;
    private $fileUploader;

    public function __construct(
        CacheManager $cacheManager,
        FileUploader $fileUploader,
        ParameterBagInterface $params
    ) {
        $this->cacheManager = $cacheManager;
        $this->fileUploader = $fileUploader;
        $this->params = $params;
    }

    /**
     * @param string $path
     * @param string $filter
     */
    public function removeCache($path)
    {
        $basePathContainer = new FilterPathContainer($path);
        $filterPathContainers = [$basePathContainer];

        foreach ($filterPathContainers as $filterPathContainer) {
            $this->cacheManager->remove($filterPathContainer->getTarget());
        }
    }

    public function initPicture($file, $alt, $title){
        $picture = new Image();
        if ($file) {
            $picture->setFile($file, $this->fileUploader);
        } else {
            $picture->setPath('');
            $picture->setCropCoordinations(null);
        }
        $picture->setAlt($alt);
        $picture->setTitle($title);

        return $picture;
    }

    public function setFileToPicture($file, $picture){
        $picture->setFile($file, $this->fileUploader);
    }

    public function deletePictureFiles($path, $picture){
        $filesystem = new Filesystem();
        try{
            $root = $this->params->get('kernel.project_dir');
            $filesystem->remove($root.'/public'.$path.'/'.$picture->getPath());
        } catch (ExceptionInterface $exception) {
            echo "An error occurred while deleting file ".$path.'/'.$picture->getPath() ;
        }
        // Remove Image cache
        $this->removeCache($path.'/'.$picture->getPath());
    }

    public function getFilterCroppingInfos($picture, String $filter){
        $filterConfiguration = $this->params->get('liip_imagine.filter_sets');
        // Read crop info in the filter
        $userFilterConfig = $filterConfiguration[$filter]['filters']['crop'];
        // Calculate aspect ratio
        $ratio = $userFilterConfig['size'][0] / $userFilterConfig['size'][1];
        // Check if crop coordinates exist for the image
        if ($picture){
            $coordinations = $picture->getCropCoordinations();
            if (!empty($coordinations)){
                // if coordinations not empty, use them to send to the template
                $coord_array = explode(',', $coordinations);
                $crop_coordinations = [ 'x' => $coord_array[0], 'y' => $coord_array[1], 'width' => $coord_array[2], 'height' => $coord_array[3]];
            } else {
                // else calculate default crop coordinations based on the image size and the aspect ratio
                $crop_coordinations = $this->calculateDefaultCropCoordinations($picture, $ratio);
            }
        } else {
            $crop_coordinations = [];
        }
        $cropConfig = ['crop_info' => $userFilterConfig, 'aspect_ratio' => $ratio, 'crop_coordinations' => $crop_coordinations];
        return $cropConfig;
    }

    public function calculateDefaultCropCoordinations($picture, $ratio){
        $coordinations = [];
        $original_width = $picture->getWidth();
        $original_height = $picture->getHeight();
        $original_ratio = $original_width / $original_height;
        if ($ratio > $original_ratio) {
            $coordinations['width'] = $original_width;
            if ($ratio >= 1){
                $coordinations['height'] = $original_width / $ratio;
            } elseif ($ratio < 1 ){
                $coordinations['height'] = $original_width * $ratio;
            }
            $coordinations['height'] = $original_width / $ratio;
            $coordinations['x'] = 0;
            $coordinations['y'] = ($original_height / 2) - ($coordinations['height'] / 2);
        } else {
            if ($ratio >= 1){
                $coordinations['width'] = $original_height / $ratio;
            } elseif ($ratio < 1 ){
                $coordinations['width'] = $original_height * $ratio;
            }
            $coordinations['height'] = $original_height;
            $coordinations['x'] = ($original_width / 2) - ($coordinations['width'] / 2);
            $coordinations['y'] = 0;
        }
        return $coordinations;
    }
}

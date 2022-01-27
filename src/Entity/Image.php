<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\Table(name="zsf2_image")
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $width;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $crop_coordinations;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="picture")
     */
    protected $user = null;

    /**
     * @ORM\OneToOne(targetEntity=Event::class, mappedBy="picture", cascade={"persist", "remove"})
     */
    private $event;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getCropCoordinations(): ?string
    {
        return $this->crop_coordinations;
    }

    public function setCropCoordinations(?string $crop_coordinations): self
    {
        $this->crop_coordinations = $crop_coordinations;

        return $this;
    }

    /**
     * Set user
     *
     * @param \App\Entity\User $user
     *
     * @return ResponsiveImage
     */
    public function setUser(\App\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    function setFile(\Symfony\Component\HttpFoundation\File\File $file, \App\Service\FileUploader $fileUploader){
        $imageFile = $fileUploader->upload($file);
        $this->setPath($imageFile['name']);
        $this->setWeight($imageFile['size']);
        $this->setWidth($imageFile['width']);
        $this->setHeight($imageFile['height']);
        $this->setCreated(new \DateTime());

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        // unset the owning side of the relation if necessary
        if ($event === null && $this->event !== null) {
            $this->event->setPictureId(null);
        }

        // set the owning side of the relation if necessary
        if ($event !== null && $event->getPictureId() !== $this) {
            $event->setPictureId($this);
        }

        $this->event = $event;

        return $this;
    }

}

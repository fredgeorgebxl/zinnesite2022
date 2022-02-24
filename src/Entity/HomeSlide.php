<?php

namespace App\Entity;

use App\Repository\HomeSlideRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=HomeSlideRepository::class)
 * @ORM\Table(name="zsf2_homeslide")
 */
class HomeSlide
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link_url;

    /**
     * @ORM\Column(type="boolean")
     */
    private $selected;

    /**
     * @ORM\OneToOne(targetEntity=Image::class, cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $video;

    /**
     * @var \DateTime $datecreated
     * 
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $datecreated;

    /**
     * @var \DateTime $datemodified
     * 
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemodified;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getLinkName(): ?string
    {
        return $this->link_name;
    }

    public function setLinkName(?string $link_name): self
    {
        $this->link_name = $link_name;

        return $this;
    }

    public function getLinkUrl(): ?string
    {
        return $this->link_url;
    }

    public function setLinkUrl(?string $link_url): self
    {
        $this->link_url = $link_url;

        return $this;
    }

    public function getSelected(): ?bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): self
    {
        $this->selected = $selected;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getDatecreated(): ?\DateTimeInterface
    {
        return $this->datecreated;
    }

    public function getDatemodified(): ?\DateTimeInterface
    {
        return $this->datemodified;
    }

}

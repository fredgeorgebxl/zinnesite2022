<?php

namespace App\Entity;

use App\Repository\TextblockRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=TextblockRepository::class)
 * @ORM\Table(name="zsf2_textblock")
 */
class Textblock
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
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

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

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $published;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDatecreated(): ?\DateTimeInterface
    {
        return $this->datecreated;
    }

    public function setDatecreated(\DateTimeInterface $datecreated): self
    {
        $this->datecreated = $datecreated;

        return $this;
    }

    public function getDatemodified(): ?\DateTimeInterface
    {
        return $this->datemodified;
    }

    public function setDatemodified(?\DateTimeInterface $datemodified): self
    {
        $this->datemodified = $datemodified;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(?bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function switchPublish(){
        if($this->published){
            $this->setPublished(FALSE);
        } else {
            $this->setPublished(TRUE);
        }
    }
}

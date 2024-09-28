<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tutoriel
 *
 * @ORM\Entity(repositoryClass=App\Repository\TutorielRepository::class)
 * @ORM\Table(name="tutoriel")
 */
class Tutoriel
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=100, nullable=false)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="format", type="string", length=100, nullable=false)
     */
    private $format;

    /**
     * @var int
     *
     * @ORM\Column(name="categorie", type="integer", nullable=false)
     */
    private $categorie;

    /**
     * @var int
     *
     * @ORM\Column(name="duree", type="integer", nullable=false)
     */
    private $duree;

    /**
     * @var string
     *
     * @ORM\Column(name="langue", type="string", length=100, nullable=false)
     */
    private $langue;

    /**
     * @ORM\Column(name="file_path", type="string", length=255, nullable=true)
     */
    private $filePath;
  /**
 * @ORM\Column(type="integer")
 */
private $views=0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getCategorie(): ?int
    {
        return $this->categorie;
    }

    public function setCategorie(int $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }
    
    /**
     * Set the number of views.
     */
    public function setViews(int $views): self
    {   
        $this->views = $views;
    
        return $this;
    }

    public function incrementViews(): self
    {
        if ($this->views === null) {
            $this->views = 0;
        }
        $this->views++;
        return $this;
    }
    
}

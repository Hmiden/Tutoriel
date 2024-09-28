<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FavorisRepository")
 * @ORM\Table(name="favoris")
 */
class Favoris
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="userid", referencedColumnName="UserID")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tutoriel")
     * @ORM\JoinColumn(name="tutorielid", referencedColumnName="id")
     */
    private $tutoriel;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTutoriel(): ?Tutoriel
    {
        return $this->tutoriel;
    }

    public function setTutoriel(?Tutoriel $tutoriel): self
    {
        $this->tutoriel = $tutoriel;

        return $this;
    }
}

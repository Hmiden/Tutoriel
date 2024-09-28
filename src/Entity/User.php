<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=App\Repository\UserRepository::class)
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="UserID", type="integer", nullable=false)
     */
    private $userid;

    /**
     * @ORM\Column(name="Nom", type="string", length=100, nullable=false)
     */
    private $nom;

    /**
     * @ORM\Column(name="Prenom", type="string", length=100, nullable=false)
     */
    private $prenom;

   

    /**
     * @ORM\Column(name="Email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(name="Password", type="string", length=100, nullable=false)
     */
    private $password;

    /**
     * @ORM\Column(name="Role", type="string", length=100, nullable=true)
     */
    private $role;
    /**
 * @ORM\Column(name="registration_date", type="datetime", nullable=false)
 */
private $registrationDate;
   


    // Getters and Setters

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

   

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }
   
    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }
    
    public function setRegistrationDate(\DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }
      
    }
?>
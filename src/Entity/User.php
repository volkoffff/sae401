<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(
    fields: "email", message: "L'adresse email est déjà utilisé"
)]
#[ApiResource()]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get', 'put'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
//    #[Assert\Email( message: " Rensseignez une email valide")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get', 'put'])]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 10, minMessage: "Votre mot de passe doit faire au moins 10 charactères")]

    private ?string $password = null;

    #[Assert\EqualTo(propertyPath:"password", message: "Les mots de passe doivents être les mêmes")]
    public $confirm_password;

    #[ORM\Column(length: 255, nullable: true)]
    private ?int $victoire = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?int $defaite = 0;

    #[ORM\Column]
    private ?int $trophee = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Avatar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $biographie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $couleur = null;


    public function __construct()
    {
        $this->partie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

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

    public function getRoles(): array
    {
        // TODO: Implement getRoles() method.
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        // TODO: Implement getUserIdentifier() method.
        return $this->email;
    }

    public function getVictoire(): ?string
    {
        return $this->victoire;
    }

    public function setVictoire(?string $victoire): self
    {
        $this->victoire = $victoire;

        return $this;
    }

    public function getDefaite(): ?string
    {
        return $this->defaite;
    }

    public function setDefaite(?string $defaite): self
    {
        $this->defaite = $defaite;

        return $this;
    }

    public function getTrophee(): ?int
    {
        return $this->trophee;
    }

    public function setTrophee(int $trophee): self
    {
        $this->trophee = $trophee;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->Avatar;
    }

    public function setAvatar(?string $Avatar): self
    {
        $this->Avatar = $Avatar;

        return $this;
    }

    public function getbiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): self
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): self
    {
        $this->couleur = $couleur;

        return $this;
    }

}

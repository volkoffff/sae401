<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(
    fields: "email", message: "L'adresse email est déjà utilisé"
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email( message: " Rensseignez une email valide")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 8, minMessage: "Votre mot de passe doit faire au moins 8 charactères")]

    private ?string $password = null;

    #[Assert\EqualTo(propertyPath:"password", message: "Les mots de passe doivents être les mêmes")]
    public $confirm_password;

    #[ORM\Column(length: 255, nullable: true)]
    private ?int $victoire = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?int $defaite = 0;

    #[ORM\Column]
    private ?int $trophee = 0;

    #[ORM\ManyToMany(targetEntity: partie::class, inversedBy: 'users')]
    private Collection $partie;

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

    /**
     * @return Collection<int, partie>
     */
    public function getPartie(): Collection
    {
        return $this->partie;
    }

    public function addPartie(partie $partie): self
    {
        if (!$this->partie->contains($partie)) {
            $this->partie->add($partie);
        }

        return $this;
    }

    public function removePartie(partie $partie): self
    {
        $this->partie->removeElement($partie);

        return $this;
    }


}

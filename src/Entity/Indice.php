<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\IndiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IndiceRepository::class)]
#[ApiResource]
class Indice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get', 'put'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get', 'put'])]
    private ?string $mot = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['get', 'put'])]
    private ?int $nbmots = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMot(): ?string
    {
        return $this->mot;
    }

    public function setMot(?string $mot): self
    {
        $this->mot = $mot;

        return $this;
    }

    public function getNbmots(): ?int
    {
        return $this->nbmots;
    }

    public function setNbmots(?int $nbmots): self
    {
        $this->nbmots = $nbmots;

        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user", referencedColumnName: "id" )]
    #[Groups(['get', 'put'])]
    private $user;


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    #[ORM\ManyToOne(inversedBy: 'partie', cascade:['persist'])]
    private ?Partie $partie = null;

    #[ORM\Column(length: 255)]
    private ?string $Action = null;

    public function getPartie(): ?Partie
    {
        return $this->partie;
    }
    public function setPartie(?Partie $partie): self
    {
        $this->partie = $partie;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->Action;
    }

    public function setAction(string $Action): self
    {
        $this->Action = $Action;

        return $this;
    }

}
<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MotPartieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MotPartieRepository::class)]
#[ApiResource]

class Motpartie
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get', 'put'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'motparties')]
    #[Groups(['get', 'put'])]
    private ?Mot $mot = null;

    #[ORM\ManyToOne(inversedBy: 'motparties', cascade:['persist'])]
    private ?Partie $partie = null;

    #[ORM\Column(length: 40)]
    #[Groups(['get', 'put'])]
    private ?string $couleurJ1 = null;

    #[ORM\Column(length: 40)]
    #[Groups(['get', 'put'])]
    private ?string $couleurJ2 = null;

    #[ORM\Column]
    #[Groups(['get', 'put'])]
    private ?int $emplacement = null;

    #[ORM\Column]
    #[Groups(['get', 'put'])]
    private ?string $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getMot(): ?Mot
    {
        return $this->mot;
    }
    public function setMot(?Mot $mot): self
    {
        $this->mot = $mot;

        return $this;
    }
    public function getPartie(): ?Partie
    {
        return $this->partie;
    }
    public function setPartie(?Partie $partie): self
    {
        $this->partie = $partie;

        return $this;
    }

    public function getCouleurJ1(): ?string
    {
        return $this->couleurJ1;
    }
    public function setCouleurJ1(string $couleurJ1): self
    {
        $this->couleurJ1 = $couleurJ1;

        return $this;
    }
    public function getCouleurJ2(): ?string
    {
        return $this->couleurJ2;
    }
    public function setCouleurJ2(string $couleurJ2): self
    {
        $this->couleurJ2 = $couleurJ2;

        return $this;
    }
    public function getEmplacement(): ?int
    {
        return $this->emplacement;
    }
    public function setEmplacement(int $emplacement): self
    {
        $this->emplacement = $emplacement;

        return $this;
    }
    public function isEtat(): ?string
    {
        return $this->etat;
    }
    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
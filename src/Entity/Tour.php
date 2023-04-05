<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TourRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourRepository::class)]
#[ApiResource]
class Tour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbtour = null;

    #[ORM\Column]
    private ?int $joueur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbtour(): ?int
    {
        return $this->nbtour;
    }

    public function setNbtour(int $nbtour): self
    {
        $this->nbtour = $nbtour;

        return $this;
    }

    public function getJoueur(): ?int
    {
        return $this->joueur;
    }

    public function setJoueur(int $joueur): self
    {
        $this->joueur = $joueur;

        return $this;
    }
}

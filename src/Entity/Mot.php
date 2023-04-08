<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MotRepository::class)]
#[ApiResource()]
class Mot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get', 'put'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get', 'put'])]
    private ?string $fr = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get', 'put'])]
    private ?string $eng = null;

    #[ORM\OneToMany(mappedBy: 'mot', targetEntity: Motpartie::class)]
    private Collection $motparties;
    public function __construct()
    {
        $this->motparties = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getFr(): ?string
    {
        return $this->fr;
    }
    public function setFr(string $fr): self
    {
        $this->fr = $fr;

        return $this;
    }
    public function getEng(): ?string
    {
        return $this->eng;
    }
    public function setEng(string $eng): self
    {
        $this->eng = $eng;

        return $this;
    }
    /**
     * @return Collection<int, Motpartie>
     */
    public function getMotparties(): Collection
    {
        return $this->motparties;
    }
    public function addMotParty(MotPartie $motParty): self
    {
        if (!$this->motparties->contains($motParty)) {
            $this->motparties->add($motParty);
            $motParty->setMot($this);
        }

        return $this;
    }

    public function removeMotParty(MotPartie $motParty): self
    {
        if ($this->motparties->removeElement($motParty)) {
            // set the owning side to null (unless already changed)
            if ($motParty->getMot() === $this) {
                $motParty->setMot(null);
            }
        }

        return $this;
    }

}

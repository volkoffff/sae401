<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PartieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PartieRepository::class)]
#[ApiResource(
    normalizationContext: [
        'groups' => ['treasure:read'],
    ],
    denormalizationContext: [
        'groups' => ['treasure:write'],
    ]
)]
class Partie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['treasure:read', 'treasure:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['treasure:read', 'treasure:write'])]
    private ?string $statut = null;

    #[ORM\Column]
    #[Groups(['treasure:read', 'treasure:write'])]
    private ?int $tour = 1;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'partie', targetEntity: Indice::class)]
    #[Groups(['treasure:read', 'treasure:write'])]
    private Collection $indices;

    // Getters and setters

    public function getIndices()
    {
        return $this->indices;
    }

    public function addIndice(Indice $indice)
    {
        if (!$this->indices->contains($indice)) {
            $this->indices[] = $indice;
            $indice->setPartie($this);
        }

        return $this;
    }

    public function removeIndice(Indice $indice)
    {
        if ($this->indices->contains($indice)) {
            $this->indices->removeElement($indice);
            // set the owning side to null (unless already changed)
            if ($indice->getPartie() === $this) {
                $indice->setPartie(null);
            }
        }

        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'partie', targetEntity: Motpartie::class)]
    #[Groups(['treasure:read', 'treasure:write'])]
    private Collection $motparties;

     public function __construct()
    {
        $this->indices = new ArrayCollection();
        $this->motparties = new ArrayCollection();
        $this->tours = new ArrayCollection();
    }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user1", referencedColumnName: "id" )]
    #[Groups(['treasure:read', 'treasure:write'])]
    private $user1;


    public function getUser1(): ?User
    {
        return $this->user1;
    }

    public function setUser1(?User $user1): self
    {
        $this->user1 = $user1;

        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user2", referencedColumnName: "id" )]
    #[Groups(['treasure:read', 'treasure:write'])]
    private $user2;

    #[ORM\Column]
    private ?string $resultat = null;

    #[ORM\Column(nullable: true)]
    private ?int $trophe = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'partie', targetEntity: Tour::class)]
    private Collection $tours;



    public function getUser2(): ?User
    {
        return $this->user2;
    }

    public function setUser2(?User $user2): self
    {
        $this->user2 = $user2;

        return $this;
    }

    public function getTour(): ?int
    {
        return $this->tour;
    }

    public function setTour(int $tour): self
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * @return Collection<int, Motpartie>
     */
    public function getMotparties(): Collection
    {
        return $this->motparties;
    }

    public function getResultat(): ?string
    {
        return $this->resultat;
    }

    public function setResultat(string $resultat): self
    {
        $this->resultat = $resultat;

        return $this;
    }

    public function getTrophe(): ?int
    {
        return $this->trophe;
    }

    public function setTrophe(?int $trophe): self
    {
        $this->trophe = $trophe;

        return $this;
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

    /**
     * @return Collection<int, Tour>
     */
    public function getTours(): Collection
    {
        return $this->tours;
    }

    public function addTour(Tour $tour): self
    {
        if (!$this->tours->contains($tour)) {
            $this->tours->add($tour);
            $tour->setPartie($this);
        }

        return $this;
    }

    public function removeTour(Tour $tour): self
    {
        if ($this->tours->removeElement($tour)) {
            // set the owning side to null (unless already changed)
            if ($tour->getPartie() === $this) {
                $tour->setPartie(null);
            }
        }

        return $this;
    }


}

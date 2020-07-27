<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * Beers
 *
 * @ORM\Table(name="beers", indexes={@ORM\Index(name="fk_beer_type", columns={"type_id"})})
 * @ORM\Entity
 */
class Beer
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="abv", type="string", length=50, nullable=false)
     */
    private $abv;

    /**
     * @var string
     *
     * @ORM\Column(name="volume", type="string", length=50, nullable=false)
     */
    private $volume;

    /**
     * @var string|null
     *
     * @ORM\Column(name="misc", type="text", length=65535, nullable=true)
     */
    private $misc;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=false)
     */
    private $img;

    /**
     * @var \Type
     * @MaxDepth(1)
     * @ORM\ManyToOne(targetEntity="App\Entity\Type")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    private $type;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @MaxDepth(1)
     * @ORM\ManyToMany(targetEntity="Brewery", inversedBy="beerid")
     * @ORM\JoinTable(name="beer_brewery",
     *   joinColumns={
     *     @ORM\JoinColumn(name="beerId", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="breweryId", referencedColumnName="id")
     *   }
     * )
     */
    private $breweries;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->breweries = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAbv(): ?string
    {
        return $this->abv;
    }

    public function setAbv(string $abv): self
    {
        $this->abv = $abv;

        return $this;
    }

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(string $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getMisc(): ?string
    {
        return $this->misc;
    }

    public function setMisc(?string $misc): self
    {
        $this->misc = $misc;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Brewery[]
     */
    public function getBreweries(): Collection
    {
        return $this->breweries;
    }

    public function addBrewery(Brewery $brewery): self
    {
        if (!$this->breweries->contains($brewery)) {
            $this->breweries[] = $brewery;
        }

        return $this;
    }

    public function removeBrewery(Brewery $brewery): self
    {
        if ($this->breweries->contains($brewery)) {
            $this->breweries->removeElement($brewery);
        }

        return $this;
    }


}

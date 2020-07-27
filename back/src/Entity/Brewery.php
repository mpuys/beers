<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * Breweries
 *
 * @ORM\Table(name="breweries", indexes={@ORM\Index(name="fk_brewery_country", columns={"country_id"})})
 * @ORM\Entity
 */
class Brewery
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var string|null
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @var \Country
     * @MaxDepth(1)
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;

    // /**
    //  * @var \Doctrine\Common\Collections\Collection
    //  * @MaxDepth(1)
    //  * @ORM\ManyToMany(targetEntity="Beer", mappedBy="brewery")
    //  */
    // private $beer;

    // /**
    //  * Constructor
    //  */
    // public function __construct()
    // {
    //     $this->beerid = new \Doctrine\Common\Collections\ArrayCollection();
    // }

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    // /**
    //  * @return Collection|Beers[]
    //  */
    // public function getBeer(): Collection
    // {
    //     return $this->beer;
    // }

    // public function addBeer(Beers $beerid): self
    // {
    //     if (!$this->beer->contains($beer)) {
    //         $this->beer[] = $beer;
    //         $beed->addBrewery($this);
    //     }

    //     return $this;
    // }

    // public function removeBeer(Beers $beer): self
    // {
    //     if ($this->beer->contains($beer)) {
    //         $this->beer->removeElement($beer);
    //         $beer->removeBrewery($this);
    //     }

    //     return $this;
    // }


}

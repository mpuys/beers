<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * Countries
 *
 * @ORM\Table(name="countries")
 * @ORM\Entity
 */
class Country /*implements \JsonSerializable*/
{
    /**
     * @var int 
     * @MaxDepth(1)
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string 
     * @MaxDepth(1)
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    // /**
    //  * @var \Brewery 
    //  * @MaxDepth(1)
    //  * @ORM\OneToMany(targetEntity="App\Entity\Brewery", mappedBy="country")
    //  */
    // private $breweries;

    // public function __construct()
    // {
    //     $this->breweries = new ArrayCollection();
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

    // /**
    //  * @return Collection|Brewery[]
    //  */
    // public function getBreweries(): Collection
    // {
    //     return $this->breweries;
    // }

    // public function jsonSerialize(): array
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name
    //     ];
    // }

    // public function addBrewery(Brewery $brewery): self
    // {
    //     if (!$this->breweries->contains($brewery)) {
    //         $this->breweries[] = $brewery;
    //         $brewery->setCountry($this);
    //     }

    //     return $this;
    // }

    // public function removeBrewery(Brewery $brewery): self
    // {
    //     if ($this->breweries->contains($brewery)) {
    //         $this->breweries->removeElement($brewery);
    //         // set the owning side to null (unless already changed)
    //         if ($brewery->getCountry() === $this) {
    //             $brewery->setCountry(null);
    //         }
    //     }

    //     return $this;
    // }


}

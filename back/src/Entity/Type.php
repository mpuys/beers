<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * Types
 *
 * @ORM\Table(name="types")
 * @ORM\Entity
 */
class Type /*implements \JsonSerializable*/
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

    // /**
    //  * @var \Beer 
    //  * @MaxDepth(1)
    //  * @ORM\OneToMany(targetEntity="App\Entity\Beer", mappedBy="type")
    //  *
    // private $beers;

    public function __construct()
    {
        $this->beers = new ArrayCollection();
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

    // /**
    //  * @return Collection|Beer[]
    //  */
    // public function getBeers(): Collection
    // {
    //     return $this->beers;
    // }

    // public function jsonSerialize(): array
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name
    //     ];
    // }

    // public function addBeer(Beer $beer): self
    // {
    //     if (!$this->beers->contains($beer)) {
    //         $this->beers[] = $beer;
    //         $beer->setType($this);
    //     }

    //     return $this;
    // }

    // public function removeBeer(Beer $beer): self
    // {
    //     if ($this->beers->contains($beer)) {
    //         $this->beers->removeElement($beer);
    //         // set the owning side to null (unless already changed)
    //         if ($beer->getType() === $this) {
    //             $beer->setType(null);
    //         }
    //     }

    //     return $this;
    // }


}

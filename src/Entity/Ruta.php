<?php

namespace App\Entity;

use App\Repository\RutaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: RutaRepository::class)]
#[Broadcast]
class Ruta implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(length: 255)]
    private ?string $localidad = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    #[ORM\Column(length: 255)]
    private ?string $fechadesde = null;

    #[ORM\Column(length: 255)]
    private ?string $fechahasta = null;

    #[ORM\Column(length: 255)]
    private ?string $programacion = null;

    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'rutas')]
    private Collection $items;

    #[ORM\OneToMany(mappedBy: 'ruta', targetEntity: Tour::class)]
    private Collection $tours;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pe_latitud = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pe_longitud = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->tours = new ArrayCollection();
    }

    public function serialize()
    {
        return [
            "id"=> $this->id,
            "titulo"=> $this->titulo,
            "descripcion"=> $this->descripcion,
            "foto"=> $this->foto,
            "fechadesde"=> $this->fechadesde,
            "fechahasta"=> $this->fechahasta,

        ];
    }

    public function jsonSerialize()
    {
        $toursData = [];
        foreach ($this->tours as $tour) {
            $toursData[] = $tour->serialize();
        }
        return [
            "id"=> $this->id,
            "titulo"=> $this->titulo,
            "descripcion"=> $this->descripcion,
            "foto"=> $this->foto,
            "fechadesde"=> $this->fechadesde,
            "fechahasta"=> $this->fechahasta,
            "tours"=>$toursData

        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getLocalidad(): ?string
    {
        return $this->localidad;
    }

    public function setLocalidad(string $localidad): static
    {
        $this->localidad = $localidad;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }

    public function getFechadesde(): ?string
    {
        return $this->fechadesde;
    }

    public function setFechadesde(string $fechadesde): static
    {
        $this->fechadesde = $fechadesde;

        return $this;
    }

    public function getFechahasta(): ?string
    {
        return $this->fechahasta;
    }

    public function setFechahasta(string $fechahasta): static
    {
        $this->fechahasta = $fechahasta;

        return $this;
    }

    public function getProgramacion(): ?string
    {
        return $this->programacion;
    }

    public function setProgramacion(string $programacion): static
    {
        $this->programacion = $programacion;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        $this->items->removeElement($item);

        return $this;
    }

    /**
     * @return Collection<int, Tour>
     */
    public function getTours(): Collection
    {
        return $this->tours;
    }

    public function addTour(Tour $tour): static
    {
        if (!$this->tours->contains($tour)) {
            $this->tours->add($tour);
            $tour->setRuta($this);
        }

        return $this;
    }

    public function removeTour(Tour $tour): static
    {
        if ($this->tours->removeElement($tour)) {
            // set the owning side to null (unless already changed)
            if ($tour->getRuta() === $this) {
                $tour->setRuta(null);
            }
        }

        return $this;
    }

    public function getPeLatitud(): ?string
    {
        return $this->pe_latitud;
    }

    public function setPeLatitud(?string $pe_latitud): static
    {
        $this->pe_latitud = $pe_latitud;

        return $this;
    }

    public function getPeLongitud(): ?string
    {
        return $this->pe_longitud;
    }

    public function setPeLongitud(?string $pe_longitud): static
    {
        $this->pe_longitud = $pe_longitud;

        return $this;
    }
}

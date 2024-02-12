<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[Broadcast]
class Item  implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $latitud = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $longitud = null;

    #[ORM\ManyToMany(targetEntity: Ruta::class, mappedBy: 'items')]
    private Collection $rutas;

    public function __construct()
    {
        $this->rutas = new ArrayCollection();
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

    public function getLatitud(): ?string
    {
        return $this->latitud;
    }

    public function setLatitud(?string $latitud): static
    {
        $this->latitud = $latitud;

        return $this;
    }

    public function getLongitud(): ?string
    {
        return $this->longitud;
    }

    public function setLongitud(?string $longitud): static
    {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * @return Collection<int, Ruta>
     */
    public function getRutas(): Collection
    {
        return $this->rutas;
    }

    public function addRuta(Ruta $ruta): static
    {
        if (!$this->rutas->contains($ruta)) {
            $this->rutas->add($ruta);
            $ruta->addItem($this);
        }

        return $this;
    }

    public function removeRuta(Ruta $ruta): static
    {
        if ($this->rutas->removeElement($ruta)) {
            $ruta->removeItem($this);
        }

        return $this;
    }


    public function jsonSerialize() {
        return [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'foto' => $this->foto,
            'id' => $this->id

        ];
    }
}

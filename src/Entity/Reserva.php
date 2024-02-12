<?php

namespace App\Entity;

use App\Repository\ReservaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservaRepository::class)]
class Reserva
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tour')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'reservas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?tour $tour = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $hora = null;

    #[ORM\OneToMany(mappedBy: 'reserva', targetEntity: Valoracion::class)]
    private Collection $valoraciones;

    public function __construct()
    {
        $this->valoraciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?user
    {
        return $this->usuario;
    }

    public function setUsuario(?user $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getTour(): ?tour
    {
        return $this->tour;
    }

    public function setTour(?tour $tour): static
    {
        $this->tour = $tour;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getHora(): ?\DateTimeInterface
    {
        return $this->hora;
    }

    public function setHora(\DateTimeInterface $hora): static
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * @return Collection<int, Valoracion>
     */
    public function getValoraciones(): Collection
    {
        return $this->valoraciones;
    }

    public function addValoracione(Valoracion $valoracione): static
    {
        if (!$this->valoraciones->contains($valoracione)) {
            $this->valoraciones->add($valoracione);
            $valoracione->setReserva($this);
        }

        return $this;
    }

    public function removeValoracione(Valoracion $valoracione): static
    {
        if ($this->valoraciones->removeElement($valoracione)) {
            // set the owning side to null (unless already changed)
            if ($valoracione->getReserva() === $this) {
                $valoracione->setReserva(null);
            }
        }

        return $this;
    }
}

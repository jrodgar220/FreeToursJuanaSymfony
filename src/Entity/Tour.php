<?php

namespace App\Entity;

use App\Repository\TourRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourRepository::class)]
class Tour implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $hora = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cancelado = null;

    #[MaxDepth(1)]
    #[ORM\ManyToOne(inversedBy: 'tours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $guia = null;

    #[ORM\ManyToOne(inversedBy: 'tours')]
    private ?Ruta $ruta = null;

    #[ORM\OneToMany(mappedBy: 'tour', targetEntity: Reserva::class, orphanRemoval: true)]
    private Collection $reservas;

    public function __construct()
    {
        $this->reservas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function isCancelado(): ?bool
    {
        return $this->cancelado;
    }

    public function setCancelado(?bool $cancelado): static
    {
        $this->cancelado = $cancelado;

        return $this;
    }

    public function getGuia(): ?User
    {
        return $this->guia;
    }

    public function setGuia(?User $guia): static
    {
        $this->guia = $guia;

        return $this;
    }

    public function getRuta(): ?Ruta
    {
        return $this->ruta;
    }

    public function setRuta(?Ruta $ruta): static
    {
        $this->ruta = $ruta;

        return $this;
    }

    /**
     * @return Collection<int, Reserva>
     */
    public function getReservas(): Collection
    {
        return $this->reservas;
    }

    public function addReserva(Reserva $reserva): static
    {
        if (!$this->reservas->contains($reserva)) {
            $this->reservas->add($reserva);
            $reserva->setTour($this);
        }

        return $this;
    }

    public function removeReserva(Reserva $reserva): static
    {
        if ($this->reservas->removeElement($reserva)) {
            // set the owning side to null (unless already changed)
            if ($reserva->getTour() === $this) {
                $reserva->setTour(null);
            }
        }

        return $this;
    }

    public function jsonSerialize() {
        $reservas = [];
        foreach ($this->reservas as $reserva) {
            $reservas[] = $reserva->serialize();
        }
        return [
            'id' => $this->id,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'cancelado' => $this->cancelado,
            'ruta' => $this->ruta,
            'guia' => $this->guia,
            'reservas' => $reservas,

        ];
    }
    public function serialize() {
        $reservas = [];
        foreach ($this->reservas as $reserva) {
            $reservas[] = $reserva->serialize();
        }
        return [
            'id' => $this->id,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'cancelado' => $this->cancelado,
            'guia' => $this->guia,
            'reservas' => $reservas

        ];
    }
}

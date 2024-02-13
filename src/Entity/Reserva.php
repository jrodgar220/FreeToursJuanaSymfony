<?php

namespace App\Entity;

use App\Repository\ReservaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservaRepository::class)]
class Reserva implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'reservas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tour $tour = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $hora = null;

    #[ORM\OneToOne(inversedBy: 'reserva')]
    private Valoracion $valoracion;

    #[ORM\Column(nullable: false)]
    private ?int $asistentes = null;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAsistentes(): ?user
    {
        return $this->asistentes;
    }

    public function setAsistentes(?int $asistentes): static
    {
        $this->asistentes = $asistentes;

        return $this;
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

    
    public function getValoracion(): Valoracion
    {
        return $this->valoracion;
    }

    

    public function setValoracion(?Valoracion $valoracion): static
    {
        $this->valoracion = $valoracion;

        return $this;
    }

    public function serialize()
    {
        return [
            "id"=> $this->id,
            "fechareserva"=> $this->fecha,
            "valoracion"=> $this->valoracion,
            "usuario"=> $this->usuario->serialize(),
            "asistentes"=>$this->asistentes

            

        ];
    }
    public function jsonSerialize()
    {
        return [
            "id"=> $this->id,
            "fechareserva"=> $this->fecha,
            "valoracion"=> $this->valoracion,
            "infotourruta"=> $this->tour,
            "asistentes"=>$this->asistentes
            

        ];
    }
}

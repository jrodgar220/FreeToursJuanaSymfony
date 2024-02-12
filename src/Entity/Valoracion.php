<?php

namespace App\Entity;

use App\Repository\ValoracionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValoracionRepository::class)]
class Valoracion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'valoraciones')]
    private ?reserva $reserva = null;

    #[ORM\Column]
    private ?int $puntuacionguia = null;

    #[ORM\Column]
    private ?int $puntuacionruta = null;

    #[ORM\Column(length: 255)]
    private ?string $comentario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReserva(): ?reserva
    {
        return $this->reserva;
    }

    public function setReserva(?reserva $reserva): static
    {
        $this->reserva = $reserva;

        return $this;
    }

    public function getPuntuacionguia(): ?int
    {
        return $this->puntuacionguia;
    }

    public function setPuntuacionguia(int $puntuacionguia): static
    {
        $this->puntuacionguia = $puntuacionguia;

        return $this;
    }

    public function getPuntuacionruta(): ?int
    {
        return $this->puntuacionruta;
    }

    public function setPuntuacionruta(int $puntuacionruta): static
    {
        $this->puntuacionruta = $puntuacionruta;

        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(string $comentario): static
    {
        $this->comentario = $comentario;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\InformeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InformeRepository::class)]
class Informe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $observaciones = null;

    #[ORM\Column]
    private ?float $dinerorecaudado = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?tour $tour = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): static
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getDinerorecaudado(): ?float
    {
        return $this->dinerorecaudado;
    }

    public function setDinerorecaudado(float $dinerorecaudado): static
    {
        $this->dinerorecaudado = $dinerorecaudado;

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
}

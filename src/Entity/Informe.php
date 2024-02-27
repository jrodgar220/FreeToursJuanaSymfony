<?php

namespace App\Entity;

use App\Repository\InformeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InformeRepository::class)]
class Informe implements \JsonSerializable
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

    #[ORM\OneToOne(inversedBy: 'informe', cascade: ['persist', 'remove'])]
    private ?Tour $tour = null;

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

    public function getTour(): ?Tour
    {
        return $this->tour;
    }

    public function setTour(?Tour $tour): static
    {
        $this->tour = $tour;

        // Establecer la relación bidireccional
        if ($tour !== null && $tour->getInforme() !== $this) {
            $tour->setInforme($this);
        }

        return $this;
    }

    public function jsonSerialize() {
        
        return [
            'id' => $this->id,
            'foto'=>$this->foto,
            'observaciones' => $this->observaciones,
            'dinerorecaudado' => $this->dinerorecaudado,
            

        ];
    }
}

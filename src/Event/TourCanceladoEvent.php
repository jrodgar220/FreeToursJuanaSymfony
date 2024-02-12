<?php
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;


class TourCanceladoEvent extends Event 
{
    public const NAME = 'tour.cancelado'; // Define la constante NAME
    private $tour;

    public function __construct($tour)
    {
        $this->tour = $tour;
    }
    public function getTour()
    {
        return $this->tour;
    }

}
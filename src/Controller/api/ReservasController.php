<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\{ReservaRepository};
use App\Entity\{Ruta,Tour,User,Reserva, Valoracion};
use Doctrine\ORM\EntityManagerInterface;





#[Route('/reservas')]
class ReservasController extends AbstractController
{ 
    //Obtiene la lista de reservas del usuario logueado
    #[Route('/', name: 'lista_reservas', methods: ['GET'])]
    public function listarReservas( Request $request,ReservaRepository $reservaRepository): Response
    {
        if($request->attributes->get('_is_user')){
            $reservas = $reservaRepository->findAllByIdUser($this->getUser()->getId());
            return new JsonResponse ($reservas, Response::HTTP_OK);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);

        
    }
    
    //Crea una reserva a partir del id del tour y del usuario registrado        
    #[Route('/crear/{id}', name: 'crear_reserva', methods: ['POST'])]
    public function crearReserva(Tour $tour,Request $request,EntityManagerInterface $entityManager): Response
    {
        //solo si está logueado puede reservar
        if($request->attributes->get('_is_user')){
            $usuario=$this->getUser();
            $body = json_decode($request->getContent(), true);
            
            $fechaHoraActual = new \DateTime();
            $fechaActual = $fechaHoraActual->format('Y-m-d'); // Formato: Año-Mes-Día
            $horaActual = $fechaHoraActual->format('H:i:s'); // Formato: Horas:Minutos:Segundos

            $fecha = new \DateTime($fechaActual);
            $hora = new \DateTime($horaActual);

            $reserva= new Reserva();
            $reserva->setUsuario($usuario);
            $reserva->setTour($tour);
            $reserva->setFecha($fecha);
            $reserva->setHora($hora);
            $reserva->setAsistentes($body['asistentes']);
            $entityManager->persist($reserva);
            $entityManager->flush();
            return new JsonResponse ($reserva, Response::HTTP_OK);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);
    }

    //Cancela una reserva pasándole su id
    #[Route('/cancelar/{id}', name: 'cancelar_reserva', methods: ['DELETE'])]
    public function cancelarReserva(Reserva $reserva,Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->attributes->get('_is_user')){
            if (!$reserva) {
                return new Response(null, Response::HTTP_NOT_FOUND);
            }
            $entityManager->remove($reserva);
            $entityManager->flush();
            return new Response("RESERVA BORRADA", Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);

    }
   
    //Valorar reserva
    #[Route('/valorar/{id}', name: 'valorar_reserva', methods: ['PUT'])]
    public function valorarReserva(Reserva $reserva,Request $request,EntityManagerInterface $entityManager): Response
    {
        $usuario=$this->getUser();
        //solo si está logueado puede reservar
        if($request->attributes->get('_is_user') 
        && $usuario->getId()==$reserva->getUsuario()->getId()){
                $body = json_decode($request->getContent(),true);
                $valoracion = new Valoracion ();
                $valoracion->setPuntuacionguia($body['puntuacionguia']);
                $valoracion->setPuntuacionruta($body['puntuacionruta']);
                $valoracion->setComentario($body['comentario']);
                
                $reserva->setValoracion($valoracion);

                $valoracion->setReserva($reserva);
                $entityManager->persist($valoracion);
                $entityManager->flush();
                return new JsonResponse ($reserva, Response::HTTP_OK);    

            
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);
    }
  
    
}

?>
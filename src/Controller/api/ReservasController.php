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
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

use App\Services\PdfGenerator;



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

    //Lista de reservas twig
    #[Route('/misreservas', name: 'mis_reservas', methods: ['GET'])]
    public function misReservas( Request $request,ReservaRepository $reservaRepository): Response
    {
        
        if($request->attributes->get('_is_user')){
            $reservas = $reservaRepository->findAllByIdUser($this->getUser()->getId());
            

            // Obtener la fecha de hoy
            $hoy = new \DateTime();
            $hoy->setTime(0, 0, 0);


            // Arreglos para las reservas pasadas y pendientes
            $reservasPasadas = [];
            $reservasPendientes = [];

            // Separar las reservas en pasadas y pendientes
            foreach ($reservas as $reserva) {
                $fechaReserva = $reserva->getTour()->getFecha()->setTime(0, 0, 0);

                if ($fechaReserva < $hoy) {
                    $reservasPasadas[] = $reserva;
                } else {
                    $reservasPendientes[] = $reserva;
                }
            }
            
            return $this->render('reservas/misreservas.html.twig', [
                'reservasPasadas' => $reservasPasadas,
                'reservasPendientes' => $reservasPendientes,
                        ]);
        }


       
    }

  
        
    
    //Crea una reserva a partir del id del tour y del usuario registrado        
    #[Route('/crear/{id}', name: 'crear_reserva', methods: ['POST'])]
    public function crearReserva(PdfGenerator $pdfGenerator, Tour $tour,Request $request,EntityManagerInterface $entityManager, MailerService $mailerService, ReservaRepository $reservaRepository): Response
    {
        //solo si está logueado puede reservar
        if($request->attributes->get('_is_user')){
            $usuario=$this->getUser();
            //si ya hay una reserva para este tour del usuario no puede reservar más
            $existeReserva=$reservaRepository->existeReserva($tour->getId(), $usuario->getId() );
            if($existeReserva)
                return new JsonResponse ("Este tour ya ha sido reservado", Response::HTTP_CONFLICT);

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
            $reserva->setAsistentes( $request->request->get('asistentes'));
            $entityManager->persist($reserva);
            $entityManager->flush();
            
           //MANDAR EMAIL DE CONFIRMACIÓN DE RESERVA 
            $cuerpomensaje="El tour " . $tour->getRuta()->getTitulo(). " con fecha ".$tour->getFecha()->format('Y-m-d')      . 
            " a las " . $tour->getHora()->format('H:i:s'). " ha sido reservado por ". $usuario->getNombre();
            $mail= $usuario->getEmail();

            $html = $this->renderView('pdfreserva.html.twig', [
                'cuerpomensaje' => $cuerpomensaje, 
                'user'  => $this->getUser()          ]);
    
            $pdfContent= $pdfGenerator->generatePdfFromHtml($html);
            

            $mailerService->mandaemail($cuerpomensaje, "Tour reservado", $mail,$pdfContent, 'file.pdf');
        
            return new JsonResponse ($reserva, Response::HTTP_CREATED);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);
    }

    
    //Cancela una reserva pasándole su id
    #[Route('/{id}', name: 'cancelar_reserva', methods: ['DELETE'])]
    public function cancelarReserva(Reserva $reserva,Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->attributes->get('_is_user')){
            if (!$reserva) {
                return new Response(null, Response::HTTP_NOT_FOUND);
            };
            

            $entityManager->remove($reserva);
            $entityManager->flush();
            return new Response("RESERVA BORRADA", Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);

    }

    //Actualiza asistentes de una reserva pasándole su id
    #[Route('/{id}', name: 'actualizar_reserva', methods: ['PUT'])]
    public function actualizarReserva(Reserva $reserva,Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->attributes->get('_is_user')    &&     $this->getUser()->getId()==$reserva->getUsuario()->getId()){
               
            $reserva->setAsistentes( $request->request->get('asistentes'));
            $entityManager->persist($reserva);
            $entityManager->flush();
            return new JsonResponse ($reserva, Response::HTTP_OK);

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
                $valoracion->setPuntuacionguia($request->request->get('puntuacionGuia'));
                $valoracion->setPuntuacionruta($request->request->get('puntuacionRuta'));
                $valoracion->setComentario($request->request->get('comentario'));
                
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
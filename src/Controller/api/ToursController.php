<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\{UserRepository,ItemRepository, TourRepository, ReservaRepository};
use App\Entity\Ruta;
use App\Entity\Tour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\TourCanceladoService;





#[Route('/tours')]
class ToursController extends AbstractController
{ 
    
    //PARA ADMINISTRADOR -----------------------------------------------------------------------------------------    
    #[Route('/vista_listado', name:'vista_lista_tour')]
    public function indexTour(Request $request): Response
    {
        if($request->attributes->get('_is_admin'))
            return $this->render('tours/listado.html.twig' );
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);

    }


    #[Route('/', name: 'lista_tours', methods: ['GET'])]
    public function listarTours(Request $request,SerializerInterface $serializer,TourRepository $tourRepository): Response
    {
        if($request->attributes->get('_is_admin')){
            $lista = $tourRepository->findAll();
            return new JsonResponse ($lista, Response::HTTP_OK);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);
       
    }


    
    #[Route('/asignarguia', name: 'asignarguia', methods: ['PUT'])]
    public function asignarGuiaATour(Request $request,EntityManagerInterface $entityManager, 
    UserRepository $userRepository,TourRepository $tourRepository): Response
    {
        if($request->attributes->get('_is_admin')){
            $datos = json_decode($request->getContent(), true);
            $guia=$userRepository->find($datos["guia"]);
            $tour=$tourRepository->find($datos["tour"]);
            $tour->setGuia($guia);
            $entityManager->persist($tour);
            $entityManager->flush();
            return new JsonResponse ($tour, Response::HTTP_OK);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);
    }

    #[Route('/cancelar/{id}', name: 'cancelar_tour', methods: ['PUT'])]
    public function cancelarTour(Request $request,Tour $tour,EntityManagerInterface $entityManager, TourCanceladoService $tourCanceladoService): Response
    {
        if($request->attributes->get('_is_admin')){
            $tour->setCancelado(true);
            $entityManager->persist($tour);
            $entityManager->flush();
            $tourCanceladoService->tourCancelado($tour->getId());
            return new JsonResponse ($tour, Response::HTTP_OK);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);

    }


    //ACESIBLE PARA EL GUIA----------------------------------------------------------
    #[Route('/asignados', name: 'tour_asignados', methods: ['GET'])]
    public function listarTourAsignados(Request $request,TourRepository $tourRepository): Response
    {
        if($request->attributes->get('_is_guia')){
            $tours=$tourRepository->findToursByGuia($this->getUser()->getId());
            return new JsonResponse ($tours, Response::HTTP_OK);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);
    }


    //Pasar lista
    #[Route('/asistencia/{id}', name: 'asistencia_tour', methods: ['PUT'])]
    public function pasarLista(Tour $tour,Request $request,EntityManagerInterface $entityManager, ReservaRepository $reservaRepository): Response
    {
        $usuario=$this->getUser();
        if($request->attributes->get('_is_guia') && $usuario->getId()==$tour->getGuia()->getId()){
                $body = json_decode($request->getContent(),true);
                //recorrer el array que me llega en json con {idusuario: xxx , asistentes:x}
                //dd($body);
                for($i=0;$i<count($body);$i++){
                    $usuario=$body[$i]['usuario'];
                    $reserva=$reservaRepository->findByTourAndUser($tour->getId(),$usuario);
                    $reserva->setAsistentes($body[$i]['asistentes']);
                    $entityManager->persist($reserva);

                }
                $entityManager->flush();

                return new JsonResponse ($tour, Response::HTTP_OK);    

            
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);
    }


    //Generar informe
    #[Route('/informe/{id}', name:'crear_ruta', methods: ['POST'])]
    public function generarInforme(Tour $tour, Request $request,EntityManagerInterface $entityManager): Response
    {
        if($$request->attributes->get('_is_guia')){
            $datos = json_decode($request->request->get('datos'), true);
            $file = $request->files->get('file');
            $fileName="";
            //Gestionar archivo
            if($file){
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('upload_informes_directory'), // en services.yaml
                        $fileName
                    );
                } catch (FileException $e) {
                }
                $filePath = $this->getParameter('upload_informes_directory') . '/' . $fileName;
            
            }

            $observaciones =$datos['observaciones'];
            $dinerorecaudado = $datos['dinerorecaudado'];
           
            $informe = new Informe();
            $informe->setObservaciones($observaciones);
            $informe->setDinerorecaudado($dinerorecaudado);
            $informe->setFoto($fileName);
                                  
            $entityManager->persist($informe);

            $entityManager->flush();

            return new JsonResponse($informe, Response::HTTP_CREATED);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);
        
    }

    //ACESIBLE PARA TODOS-----------------------------------------------------------------
    #[Route('/{id}', name: 'tour', methods: ['GET'])]
    public function verTour(Tour $tour): Response
    {
        return new JsonResponse ($tour, Response::HTTP_OK);
    }


    


   
    

   

    

    

    

    
    
}

?>
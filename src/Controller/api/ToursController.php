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
use App\Repository\{UserRepository,ItemRepository, TourRepository};
use App\Entity\Ruta;
use App\Entity\Tour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\TourCanceladoService;





#[Route('/tours')]
class ToursController extends AbstractController
{ 
    
   

    #[Route('/vista_listado', name:'vista_lista_tour')]
    public function indexTour(): Response
    {
        return $this->render('tours/listado.html.twig', );
    }


    #[Route('/', name: 'lista_tours', methods: ['GET'])]
    public function listarTours(SerializerInterface $serializer,TourRepository $tourRepository): Response
    {
        $lista = $tourRepository->findAll();
        return new JsonResponse ($lista, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'tour', methods: ['GET'])]
    public function verTour(Tour $tour): Response
    {
        return new JsonResponse ($tour, Response::HTTP_OK);
    }

    #[Route('/asignarguia', name: 'asignarguia', methods: ['PUT'])]
    public function asignarGuiaATour(Request $request,EntityManagerInterface $entityManager, 
    UserRepository $userRepository,TourRepository $tourRepository): Response
    {
        $datos = json_decode($request->getContent(), true);
        $guia=$userRepository->find($datos["guia"]);
        $tour=$tourRepository->find($datos["tour"]);
        $tour->setGuia($guia);
        $entityManager->persist($tour);
        $entityManager->flush();

        return new JsonResponse ($tour, Response::HTTP_OK);
    }

    #[Route('/cancelar/{id}', name: 'cancelar_tour', methods: ['PUT'])]
    public function cancelarTour(Tour $tour,EntityManagerInterface $entityManager, TourCanceladoService $tourCanceladoService): Response
    {
        $tour->setCancelado(true);
        $entityManager->persist($tour);
        $entityManager->flush();
        $tourCanceladoService->tourCancelado($tour->getId());
        return new JsonResponse ($tour, Response::HTTP_OK);
    }

    //para probar el evento propio
    #[Route('/evento/lanzar', name: 'cancelar_tour_evento_prueba', methods: ['GET'])]
    public function cancelarTourE( TourCanceladoService $tourCanceladoService): Response
    {
        $tourCanceladoService->tourCancelado("9");
        return new JsonResponse ("OK", Response::HTTP_OK);
    }


   
    

   

    

    

    

    
    
}

?>
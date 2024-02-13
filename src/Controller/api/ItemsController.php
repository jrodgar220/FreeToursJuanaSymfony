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
use App\Repository\ItemRepository;
use Symfony\Component\Serializer\SerializerInterface;



#[Route('/items')]
class ItemsController extends AbstractController
{ 
    #[Route('/', name: 'items', methods: ['GET'])]
    public function listarUsuarios(SerializerInterface $serializer, ItemRepository $itemRepository): Response
    {
        $listaItems = $itemRepository->findAll();
        return new JsonResponse ($listaItems, Response::HTTP_OK);
    }

    


    

    

    

    
    
}

?>
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
use App\Repository\UserRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


#[Route('/usuarios')]
class UsuariosController extends AbstractController
{ 
    #[Route('/', name:'mostrar_usuarioS', methods: ['GET'])]
    public function show(SerializerInterface $serializer,UserRepository $userRepository): Response
    {
        $listaUsuarios = $userRepository->findAll();
        $usuariosSerializados=[];
        foreach ($listaUsuarios as $usuario){
            $usuariosSerializados[]= $usuario->serialize();
        }
        return new JsonResponse ($usuariosSerializados, Response::HTTP_OK);
    }

    #[Route('/guias', name:'listar_guias', methods: ['GET'])]
    public function obtenerGuias(SerializerInterface $serializer, UserRepository $userRepository): Response
    {
        $listaGuias= $userRepository->findAllGuias();
        $usuariosSerializados=[];
        foreach ($listaGuias as $usuario){
            $usuariosSerializados[]= $usuario->serialize();
        }
        return new JsonResponse($usuariosSerializados, JsonResponse::HTTP_OK);
     
    }

    
}

?>
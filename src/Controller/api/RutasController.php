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
use App\Repository\{UserRepository,ItemRepository, RutaRepository,TourRepository};
use App\Entity\Ruta;
use App\Entity\Tour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;




#[Route('/rutas')]
class RutasController extends AbstractController
{ 
   

    public function crearTour($arrayhorasdia, $guias, $fecha, $ruta,  $userRepository ){
        //array horas siempre tiene mínimo un elemento aunque sea vacío, 
        //por lo que hay que comprobarlo
        $tours=[];
        if($arrayhorasdia[0]!=""){

            for($i=0;$i<count($arrayhorasdia);$i++)  {
                // Crear el objeto tour
                $tour = new Tour();
                $tour->setHora(\DateTime::createFromFormat('H:i', $arrayhorasdia[$i]));
                $tour->setFecha(\DateTime::createFromFormat('d/m/Y', $fecha->format('d/m/Y')));
                $tour->setGuia($userRepository->find($guias[$i]));
                $tour->setRuta($ruta);
                $ruta->addTour($tour);
               $tours[]=$tour;
            }
        return $tours;
        }

    }
   
    //API PARA ADMIN--------------------------------------------------------
    #[Route('/nueva', name:'vista_form_ruta')]
    public function formRuta( Request $request): Response
    {  
        $isAdmin = $request->attributes->get('_is_admin');
        if($isAdmin)
            return $this->render('rutas/index.html.twig');
        
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);
    }

    #[Route('/listado', name:'vista_lista_ruta')]
    public function indexRuta( Request $request,RutaRepository $rutaRepository): Response
    {
        if($request->attributes->get('_is_admin')){
            $listaRutas = $rutaRepository->findAll();
            return $this->render('rutas/listado.html.twig', [
                'listaRutas'=> $listaRutas
            ]);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);

    }

    #[Route('/', name: 'lista_rutas', methods: ['GET'])]
    public function listarRutas( Request $request,SerializerInterface $serializer,RutaRepository $rutaRepository): Response
    {
        if($request->attributes->get('_is_admin')){
            $listaRutas = $rutaRepository->findAll();
            $rutasSerializadas=[];
            foreach ($listaRutas as $ruta){
                $rutasSerializadas[]= $ruta->serialize();
            }
            return new JsonResponse ($rutasSerializadas, Response::HTTP_OK);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);

        
    }

    #[Route('/crear', name:'crear_ruta', methods: ['POST'])]
    public function crearRuta(Request $request,EntityManagerInterface $entityManager, UserRepository $userRepository, ItemRepository $itemRepository): Response
    {
        if($$request->attributes->get('_is_admin')){
            $datos = json_decode($request->request->get('datos'), true);
            $file = $request->files->get('file');
            $fileName="";
            //Gestionar archivo
            if($file){
                // Genera un nombre único para el archivo
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                // Mueve el archivo al directorio donde deseas almacenarlo
                try {
                    $file->move(
                        $this->getParameter('upload_rutas_directory'), // en services.yaml
                        $fileName
                    );
                } catch (FileException $e) {
                }
                // Obtén la ruta completa al archivo
                $filePath = $this->getParameter('upload_rutas_directory') . '/' . $fileName;
            
            }


            // Obtener datos del formulario
            $titulo =$datos['titulo'];
            $descripcion = $datos['descripcion'];
            $fechadesde = $datos['fechadesde'];
            $fechahasta = $datos['fechahasta'];
            $localidad = $datos['localidad'];
            $lunes = $datos['lunes'];
            $martes = $datos['martes'];
            $miercoles = $datos['miercoles'];
            $jueves = $datos['jueves'];
            $viernes = $datos['viernes'];
            $sabado = $datos['sabado'];
            $domingo = $datos['domingo'];
            $guias = $datos['guias'];
            $items = $datos['items'];

            //TODO:falta el punto de encuentro
            
        
            // Convertir las fechas en objetos DateTime
            $fechaDesdeObj = \DateTime::createFromFormat('d/m/Y', $fechadesde);
            $fechaHastaObj = \DateTime::createFromFormat('d/m/Y', $fechahasta);
            
        
            // Crear el objeto Ruta
            $ruta = new Ruta();
            $ruta->setTitulo($titulo);
            $ruta->setDescripcion($descripcion);
            $ruta->setFechadesde($fechadesde);
            $ruta->setFechahasta($fechahasta);
            $ruta->setLocalidad($localidad);
            //foto
            $ruta->setFoto($fileName);
            //programacion
            $programacion = [
                "lunes" => $lunes,
                "martes" => $martes,
                "miercoles" => $miercoles,
                "jueves" => $jueves,
                "viernes" => $viernes,
                "sabado" => $sabado,
                "domingo" => $domingo,
                "guias"=>$guias
            ];
            $ruta->setProgramacion(json_encode($programacion));
            //items
            for($i=0;$i<count($items);$i++) {
                $ruta->addItem($itemRepository->find($items[$i]));
            }

            $entityManager->persist($ruta);

            //PERSISTIR LOS TOURS
            // Recorrer el periodo de fechas
            while ($fechaDesdeObj <= $fechaHastaObj) {
                // Obtener el día de la semana como número (1: lunes, 2: martes, 3: miércoles, etc.)
                $diaSemana = (int)$fechaDesdeObj->format('N');
                switch ($diaSemana) {
                    case 1://LUNES
                        $tour= $this->crearTour($lunes, $guias, $fechaDesdeObj , $ruta, $userRepository);
                        break;
                    case 2: //MARTES
                        $tour= $this->crearTour($martes, $guias, $fechaDesdeObj , $ruta, $userRepository);
                        break;
                    case 3://MIÉRCOLES
                        $tour= $this->crearTour($miercoles, $guias, $fechaDesdeObj , $ruta, $userRepository);
                        break;
                    case 4:
                        $tour= $this->crearTour($jueves, $guias, $fechaDesdeObj , $ruta, $userRepository);
                        break;
                    case 5:
                        $tour= $this->crearTour($viernes, $guias, $fechaDesdeObj , $ruta, $userRepository);
                        break;
                    case 6:
                        $tour= $this->crearTour($sabado, $guias, $fechaDesdeObj , $ruta, $userRepository);
                        break;
                    case 7:
                        $tour= $this->crearTour($domingo, $guias, $fechaDesdeObj , $ruta, $userRepository);
                        break;
                }
                if($tour != null && count($tour)>0)
                    foreach ($tour as $t){
                        $entityManager->persist($t);
                    }
                // Incrementar la fecha en un día
                $fechaDesdeObj->modify('+1 day');
            }


            $entityManager->flush();

            return new JsonResponse($datos, Response::HTTP_CREATED);
        }
        return new JsonResponse ("NO AUTORIZADO", Response::HTTP_FORBIDDEN);
        
    }


    //ACCESIBLE PARA TODOS-----------------------------------------------------------------------------------------
    #[Route('/{id}', name: 'ruta', methods: ['GET'])]
    public function verRuta(Ruta $ruta): Response
    {
        return new JsonResponse ($ruta, Response::HTTP_OK);
    }

    

    #[Route('/obtener', name:'lista_filtro', methods: ['POST'])]
    public function obtenerRutas(Request $request,TourRepository $tourRepository,RutaRepository $rutaRepository): Response
    {
        $datos = json_decode($request->getContent(), true);
        $rutas=[];
        if( $datos["fecha"]!="" ){
            $tours = $tourRepository->findToursByFecha($datos["fecha"]);
            foreach ($tours as $tour){
                if($tour->getRuta()->getLocalidad()==$datos["localidad"])
                    $rutas[$tour->getRuta()->getId()]= $tour->getRuta()->serialize();
            }
        }else{
            $rutasbd = $rutaRepository->findRutasByLocalidad($datos["localidad"]);
            foreach ($rutasbd as $ruta){
                $rutas[$ruta->getId()]= $ruta->serialize();
            }
        
        }



        return new JsonResponse($rutas, 200);

        
    }
   
    

   

    

    

    

    
    
}

?>
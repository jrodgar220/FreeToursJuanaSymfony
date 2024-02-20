<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Alumno;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use App\Services\MailerService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\TourCanceladoService;

class home extends AbstractController
{
    #[Route('/', name:'home')]
    public function inicio():Response 
    {
                
        return $this->render('home.html.twig');
    }

    #[Route('/pdf', name:'pdf')]
    public function pdf(Pdf $knpSnappyPdf):Response 
    {
        $html = $this->renderView('pdfreserva.html.twig');

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'file.pdf'
        );

    }

    #[Route('/mail', name:'mailer', methods: ['GET'])]
    public function mailer(MailerService $mailerService):Response 
    {
        $mail = $mailerService->mandaemail();
        return new JsonResponse ($mail, Response::HTTP_OK);
            
        
    }

    
    #[Route('/email', name:'mandamaildirecto')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('jrodgar220@g.educaand.es')
            ->to('jmrg00021@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        return new Response ("Enviado");

        
    }


   
    
}

?>
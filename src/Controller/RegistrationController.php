<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //Gestionar el archivo
            $file = $form->get('foto')->getData();
            if($file){
                // Genera un nombre único para el archivo
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                // Mueve el archivo al directorio donde deseas almacenarlo
                try {
                    $file->move(
                        $this->getParameter('your_upload_directory'), // en services.yaml
                        $fileName
                    );
                } catch (FileException $e) {
                }
                // Obtén la ruta completa al archivo
                $filePath = $this->getParameter('your_upload_directory') . '/' . $fileName;
            
                $user->setFoto($filePath);
            }
            $user->setRoles(['ROLE_USER']); // Asigna el rol "Cliente"
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

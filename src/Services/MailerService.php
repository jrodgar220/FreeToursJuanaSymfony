<?php 

// src/Controller/ProductController.php
namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
    ) {    }


    public function mandaemail($info): bool
    {

        $email = (new Email())
        ->from('jrodgar220@g.educaand.es')
        ->to('jmrg00021@gmail.com')
        ->subject('Time for Symfony Mailer! ' .$info)
        ->text('Sending emails is fun again!')
        ->html('<p>See Twig integration for better HTML integration!</p>');

        $this->mailer->send($email);

      

        return true;
    }
}

?>
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{

    public function index(): Response
    {
        return $this->render('welcome.html.twig', [
            'day' => date('l'),
            'cool' => '<strong>December</strong>'
        ]);
    }

}

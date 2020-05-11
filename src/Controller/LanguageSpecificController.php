<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LanguageSpecificController extends AbstractController
{
    /**
     * @Route("/language-test", name="language_specific")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'LanguageSpecificController',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\AnnonceRepository;

class HomeController extends AbstractController
{
    #[Route('/')]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findLatestNotSold();
        return $this->render('home/index.html.twig', [
            'current_menu' => 'app_home_index',
            'annonces' => $annonces
        ]);        

        return $this->render('home/index.html.twig', [
            'title' => 'Bienvenue sur Duckzon',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem quam cum corrupti modi cupiditate nostrum odit illo veniam, nulla neque officia expedita rerum, aliquid libero incidunt rem iusto reprehenderit maxime!',
            'createdAt' => new \DateTime() 
        ]);
    }
}



// namespace App\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;


// class HomeController extends AbstractController
// {
//     #[Route('/home', name: 'app_annonce')]
//     public function index(): Response
//     {
//         return $this->render('home/index.html.twig', [
//             'controller_name' => 'HomeController',
//         ]);
//     }
// }










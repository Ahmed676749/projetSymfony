<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Annonce;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\AnnonceRepository;

class AnnonceController extends AbstractController
{
    // #[Route('/annonce')]
    // public function index(): Response
    // {
    //     return $this->render('annonce/index.html.twig', [
    //         'controller_name' => 'AnnonceController',
    //         'current_menu' => 'app_annonce_index',
    //     ]);
    // }

    #[Route('/annonce/new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine): Response
    {
        $annonce = new Annonce();
        $annonce
            ->setTitle('Ma collection de canard')
            ->setDescription('Vends car plus d\'utilité')
            ->setPrice(10)
            ->setStatus(Annonce::STATUS_BAD)
            ->setIsSold(false)
     
           
        ;
        
        // On récupère l'EntityManager
        $em = $doctrine->getManager();
        // On « persiste » l'entité
        $em->persist($annonce);
        // On envoie tout ce qui a été persisté avant en base de données
        $em->flush();

        return new Response('annonce bien créée');

        dump($annonce);
        die;
        
        // dd($annonce); permet de faire la même chose que dump($annonce); die;
    }

    #[Route('/annonce', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        // rechercher une annonce par ID
        $annonce = $annonceRepository->find(1);
        dump($annonce);
        // recherche toutes les annonces
        $annonce = $annonceRepository->findAll();
        dump($annonce);
        // recherche une annonce par champ
        $annonce = $annonceRepository->findOneBy(['isSold' => false]);
        dump($annonce);

        $annonces = $annonceRepository->findAllNotSold();
        dump($annonces);

        return $this->render('annonce/index.html.twig', [
            'current_menu' => 'app_annonce_index',
            'annonces' => $annonces
        ]);
    }

    #[Route('/annonce/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], methods: ['GET'])]
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/annonce/{id}/edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Annonce $annonce): Response
    {

        
        $formBuilder = $this->createFormBuilder($annonce); // on crée un objet FormBuilder qui permet de construire le formulaire
        $formBuilder->add('title'); // on ajoute des champs
        $formBuilder->add('description', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class); // on peut changer le type de champs (on aurait pu utiliser un use, mais pour plus de simplicité, j'ai écrit l'espace de nom directement)
        $form = $formBuilder->getForm(); // on utilise la fonction getForm afin que le FormBuilder nous renvoie un objet de type FormInterface
        $formView = $form->createView(); // grâce à cet objet FormInterface, on peut construire la vue Twig avec createView
        $form = $this->createForm(AnnonceType::class, $annonce);
        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView()
        ]);
    }
}



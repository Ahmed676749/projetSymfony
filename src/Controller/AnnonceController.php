<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


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
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em)
    {
        $annonce = new Annonce();
    
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annonce);
            $em->flush();
            return $this->redirectToRoute('app_annonce_index');
        }
            
        return $this->render('annonce/new.html.twig', [
            'annonce' => $annonce,
            'formView' => $form->createView()
        ]);
    }

    #[Route('/annonce', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $annonces = $paginator->paginate(
            $annonceRepository->findAllNotSoldQuery(),
            $request->query->getInt('page', 1), // on récupère le paramètre page en GET. Si le paramètre page n'existe pas dans l'url, la valeur par défaut sera 1
            12 // on veut 12 annonces par page
        );

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
    #[Security("is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and annonce.getUser() == user)")]
    public function edit(Annonce $annonce, Request $request, EntityManagerInterface $em)
    {

        
        $formBuilder = $this->createFormBuilder($annonce); // on crée un objet FormBuilder qui permet de construire le formulaire
        $formBuilder->add('title'); // on ajoute des champs
        $formBuilder->add('description', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class); // on peut changer le type de champs (on aurait pu utiliser un use, mais pour plus de simplicité, j'ai écrit l'espace de nom directement)
        $form = $formBuilder->getForm(); // on utilise la fonction getForm afin que le FormBuilder nous renvoie un objet de type FormInterface
        $formView = $form->createView(); // grâce à cet objet FormInterface, on peut construire la vue Twig avec createView
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request); // on dit au formulaire d'écouter la requête

        if ($form->isSubmitted() && $form->isValid()) { // si le formulaire est envoyé et s'il est valide
            $em->flush();
            $this->addFlash('success', 'Annonce modifiée avec succès');
            return $this->redirectToRoute('app_annonce_index');
        }

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'formView' => $form->createView()
        ]);
    }

    #[Route('/annonce/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[Security("is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and annonce.getUser() == user)")]
        public function delete(Annonce $annonce, EntityManagerInterface $em, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->get('_token'))) {
            $em->remove($annonce);
            $em->flush();
        }
        return $this->redirectToRoute('app_annonce_index');
    }
}



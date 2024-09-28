<?php
// src/Controller/SearchController.php

namespace App\Controller;
use App\Entity\Historique;
use App\Entity\Tutoriel;
use App\Entity\Favoris;
use App\Form\SearchType;
use App\Repository\TutorielRepository;
use App\Repository\FavorisRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WebScraperService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\UserRepository;
use App\Repository\HistoriqueRepository;

use Doctrine\ORM\EntityManagerInterface;


class SearchController extends AbstractController
{
/**
 * @Route("/search", name="app_search")
 */
public function search(
    Request $request, 
    TutorielRepository $repository, 
    WebScraperService $webScraper, 
    SessionInterface $session, 
    UserRepository $userRepository
    


) {
    // Retrieve data from the GET request
    $titre = $request->query->get('titre');
    $format = $request->query->get('format');
    $duree = $request->query->get('duree');
    $langue = $request->query->get('langue');

    $tutoriels = [];

    $queryBuilder = $repository->createQueryBuilder('t');

    // Filtering the local database based on the query parameters
    if (!empty($titre)) {
        $queryBuilder->andWhere('t.titre LIKE :titre')
                     ->setParameter('titre', '%' . $titre . '%');
    }

    if (!empty($format)) {
        $queryBuilder->andWhere('t.format = :format')
                     ->setParameter('format', $format);
    }

    if (!empty($duree)) {
        if ($duree == 30) {
            $queryBuilder->andWhere('t.duree < :duree')
                         ->setParameter('duree', 30);
        } elseif ($duree == 61) {
            $queryBuilder->andWhere('t.duree >= :duree')
                         ->setParameter('duree', 30);
        }
    }

    if (!empty($langue)) {
        $queryBuilder->andWhere('t.langue = :langue')
                     ->setParameter('langue', $langue);
    }

    $tutoriels = $queryBuilder->getQuery()->getResult();

    
    // Retrieve the user ID from the session
    $userId = $session->get('user_id');
    $user = $userRepository->find($userId);

    if (!$user) {
        // Handle the case where the user is not found (e.g., redirect to login)
        return $this->redirectToRoute('Connexionuser');
    }
      // Log the search in the historique table
      

    // Render the search form template with user information and tutorial results
    return $this->render('search_form.html.twig', [
        'tutoriels' => $tutoriels,
        'user' => $user,
    ]);
}

/**
 * @Route("/test-web-scraping", name="test_web_scraping")
 */
public function testWebScraping(WebScraperService $webScraper)
{
    //$scrapedCoursera = $webScraper->scrapeCoursera();
    //$scrapedEdx = $webScraper->scrapeEdx();

    return $this->json([
       // 'coursera' => $scrapedCoursera,
        //'edx' => $scrapedEdx,
    ]);
}



/**
 * @Route("/search_resulat", name="app_search_results")
 */
public function search_resulat(Request $request, SessionInterface $session,  EntityManagerInterface $entityManager, TutorielRepository $tutorielRepository, UserRepository $userRepository, WebScraperService $webScraper)
{
    // Récupération des critères de recherche
    $titre = $request->query->get('titre', '');
    $format = $request->query->get('format', '');
    $duree = $request->query->get('duree', '');
    $langue = $request->query->get('langue', '');
   
    // Recherche dans la base de données
    $queryBuilder = $tutorielRepository->createQueryBuilder('t');

    if (!empty($titre)) {
        $queryBuilder->andWhere('t.titre LIKE :titre')
                     ->setParameter('titre', '%' . $titre . '%');
    }

    if (!empty($format)) {
        $queryBuilder->andWhere('t.format = :format')
                     ->setParameter('format', $format);
    }

    if (!empty($duree)) {
        if ($duree == 30) {
            $queryBuilder->andWhere('t.duree < :duree')
                         ->setParameter('duree', 30);
        } elseif ($duree == 61) {
            $queryBuilder->andWhere('t.duree >= :duree')
                         ->setParameter('duree', 30);
        }
    }

    if (!empty($langue)) {
        $queryBuilder->andWhere('t.langue = :langue')
                     ->setParameter('langue', $langue);
    }

    $tutorials = $queryBuilder->getQuery()->getResult();

    // Ajouter des résultats de scraping
    //$scrapedCoursera = $webScraper->scrapeCoursera();
    //$scrapedEdx = $webScraper->scrapeEdx();

    // Mapper les résultats de scraping à une structure similaire à Tutoriel
    $scrapedResults = array_map(function ($title) {
        $tutoriel = new \stdClass();
        $tutoriel->titre = $title;
        return $tutoriel;
    }, array_merge(   ));

    // Combiner les résultats de la base de données et du web scraping
    $allResults = array_merge($tutorials, $scrapedResults);
    $userId = $session->get('user_id');
    $user = $userRepository->find($userId);

    if (!$user) {
        // Handle the case where the user is not found (e.g., redirect to login)
        return $this->redirectToRoute('Connexionuser');
    }
    $historique = new Historique();
    $historique->setUserid($user->getUserid());
    $historique->setDate(new \DateTime()); // Set current date and time
    $historique->setText('Recherche effectuée avec les critères: Titre - ' . $titre . ', Format - ' . $format . ', Durée - ' . $duree . ', Langue - ' . $langue);

    $entityManager->persist($historique);
    $entityManager->flush();
    // Renvoyer les résultats à la vue
    return $this->render('search_results.html.twig', [
        'tutorials' => $allResults,
        'titre' => $titre, // Passer le titre à la vue pour l'afficher
        'user' => $user,
        

    ]);
}
 /**
     * @Route("/tutoriel/{id}", name="tutoriel_show")
     */
    public function show(int $id, TutorielRepository $tutorielRepository): Response
    {
        $tutoriel = $tutorielRepository->find($id);

        if (!$tutoriel) {
            throw $this->createNotFoundException('The tutorial does not exist');
        }

        return $this->render('show.html.twig', [
            'tutoriel' => $tutoriel,
        ]);
    }

    // Existing methods...
/**
 * @Route("/tutoriel/{id}/{format}/{titre}", name="view_tutoriel", methods={"GET"})
 */
public function viewTutoriel(int $id, string $format, string $titre): Response
{
    $tutorial = $this->getDoctrine()->getRepository(Tutoriel::class)->find($id);

    if ($tutorial) {
        $tutorial->incrementViews();
        $em = $this->getDoctrine()->getManager();
        $em->persist($tutorial);
        $em->flush();

        // Déterminer le chemin du fichier en fonction du format
        $filePath = $this->generateUrl('tutoriel_file', ['format' => $format, 'titre' => $titre]);

        // Rediriger vers le fichier demandé
        return $this->redirect($filePath);
    }

    throw $this->createNotFoundException('Tutoriel non trouvé');
}

/**
 * @Route("/tutoriel/{format}/{titre}", name="tutoriel_file", methods={"GET"})
 */
public function tutorielFile(string $format, string $titre): Response
{
    $filePath = $this->getParameter('kernel.project_dir') . '/public/tutoriel/' . $format . '/' . $titre;

    if (file_exists($filePath)) {
        return new BinaryFileResponse($filePath);
    }

    throw $this->createNotFoundException('Fichier non trouvé');
}


/**
     * @Route("/profil/historique", name="user_historique_profile")
     */
    public function userHistoriqueProfile(
        SessionInterface $session, 
        HistoriqueRepository $historiqueRepository, 
        UserRepository $userRepository
    ) {
        // Retrieve the user ID from the session
        $userId = $session->get('user_id');
        $user = $userRepository->find($userId);

        if (!$user) {
            // Handle the case where the user is not found (e.g., redirect to login)
            return $this->redirectToRoute('Connexionuser');
        }

        // Retrieve the user's historique from the repository
        $historique = $historiqueRepository->findBy(['userid' => $userId], ['date' => 'DESC']);

        // Render the profile template with the historique
        return $this->render('historique.html.twig', [
            'historique' => $historique,
            'user' => $user,
        ]);
    }
 /**
     * @Route("/add_favori/{id}", name="add_favori")
     */
    public function addFavori($id, Request $request, TutorielRepository $tutorielRepository, FavorisRepository $favorisRepository, UserRepository $userRepository): Response
    {
        // Retrieve the session
        $session = $request->getSession();
        $userId = $session->get('user_id');
        
        if (!$userId) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter des favoris.');
            return $this->redirectToRoute('login'); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
        }

        // Find the tutorial
        $tutorial = $tutorielRepository->find($id);
        
        if (!$tutorial) {
            throw $this->createNotFoundException('No tutorial found for id '.$id);
        }

        // Find the user
        $user = $userRepository->find($userId);
        
        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$userId);
        }

        // Check if the favori already exists
        $existingFavori = $favorisRepository->findOneBy([
            'user' => $user,
            'tutoriel' => $tutorial,
        ]);

        if ($existingFavori) {
            $this->addFlash('info', 'Ce tutoriel est déjà dans vos favoris.');
            return $this->redirectToRoute('app_search'); // Redirige vers la page de recherche
        }

        // Create and save the new favorite entry
        $favori = new Favoris();
        $favori->setUser($user); // Utilise l'objet User
        $favori->setTutoriel($tutorial);
        $favori ->setDate(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($favori);
        $em->flush();
        
        $this->addFlash('success', 'Tutoriel ajouté aux favoris.');
        return $this->redirectToRoute('app_search'); // Redirige vers la page de recherche
    }



/**
     * @Route("/favoris", name="favoris")
     */
    public function listFavorites(Request $request,FavorisRepository $favorisRepository, TutorielRepository $tutorielRepository, UserRepository $userRepository): Response
    {
        // Retrieve the session
        $session = $request->getSession();
        $userId = $session->get('user_id');
        // Find the user
        $user = $userRepository->find($userId);
        
        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$userId);
        }

        // Fetch favorite entries for the logged-in user
        $favorites = $favorisRepository->findBy(['user' => $userId]);

        // Fetch details of each favorite tutorial
        $favoriteTutorials = [];
        foreach ($favorites as $favorite) {
            $tutorial = $tutorielRepository->find($favorite->getTutoriel());
            if ($tutorial) {
                $favoriteTutorials[] = $tutorial;
            }
        }

        return $this->render('favoris.html.twig', [
            'favoriteTutorials' => $favoriteTutorials,
            'user' => $user,

        ]);









}}
<?php
// src/Controller/TutorielController.php

namespace App\Controller;

use App\Repository\TutorielRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository; // Correctly import the UserRepository


class StatistiqueController extends AbstractController
{
    /**
     * @Route("/most-viewed", name="most_viewed_tutorials")
     */
    public function mostViewed(TutorielRepository $tutorielRepository): Response
    {
        $mostViewedTutorials = $tutorielRepository->findMostViewed();

        return $this->render('tutoriel/most_viewed.html.twig', [
            'tutorials' => $mostViewedTutorials,
        ]);
    }
      /**
     * @Route("/statistics", name="statistics")
     */
    public function index(UserRepository $userRepo): Response
    {
        $totalUsers = $userRepo->countTotalUsers();

        // For today's registrations
        $today = new \DateTime();
        $usersRegisteredToday = $userRepo->countUsersRegisteredOnDay($today);

        // For the last 7 days
        $data = [];
        $labels = [];
        $values = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = (clone $today)->modify("-{$i} days");
            $count = $userRepo->countUsersRegisteredOnDay($day);
            $labels[] = $day->format('l'); // 'l' formats the date as the full day name (e.g., "Monday")
            $values[] = $count;
        }

        return $this->render('statistics.html.twig', [
            'totalUsers' => $totalUsers,
            'usersRegisteredToday' => $usersRegisteredToday,
            'registrationLabels' => $labels,
            'registrationValues' => $values,
        ]);
    }
}


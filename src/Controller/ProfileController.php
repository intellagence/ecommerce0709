<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mon-compte')]
final class ProfileController extends AbstractController
{
    #[Route('', name: 'app_profile_index')]
    public function index(): Response
    {
        // dd($this->getUser());
        return $this->render('profile/index.html.twig', []);
    }
}

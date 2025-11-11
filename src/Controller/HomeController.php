<?php

namespace App\Controller; // App = le dossier src

// Toutes les class dans le fichier doivent être importées
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class HomeController extends AbstractController // extends : héritage
{

    /*
        Pour rappel, une méthode(= fonction) doit être appelée pour être exécutée.
        ici c'est la route qui va lancer la méthode située en dessous

        Dans la route, il y a plusieurs arguments :

        le premier est la route (cad, ce qu'on retrouve dans l'url à la suite du serveur)
        exemple : en local : 127.0.0.1:8000 + contacténation des routes
        pour la route actuelle 127.0.0.1:8000 + /catalogue
        en ligne 127.0.0.1:8000 devient nomDeDomaine.fr

        l'argument name:'' permet de rediriger (par les liens)
    */

    #[Route('/catalogue', name:'app_catalog')]
    public function catalog(): Response
    {
        // dd('ok'); // dump die

        /* 
            Cette route doit retourner une vue html donc la méthode de la route va utiliser le terme "return" et employer la méthode render() provenant de la class héritée AbstractController

            Cette méthode permet de rendre une vue provenant du dossier templates et elle se déplace directement à la racine de ce dernier
            Cette méthode a besoin de 2 arguments
            - 1e OBLIGATOIRE (type : string) : nom du fichier ainsi que son emplacement dans le dossier templates
            - 2e FACULTATIF (type : array) : tableau des données
        */
        return $this->render('home/catalog.html.twig');
    }


    #[Route('/', name:'app_home')]
    public function home(): Response
    {
        $firstNameController = 'Ugo';

        $fruits = ['fraise', 'banane', 'pomme', 'kiwi'];

        //dump($firstNameController);

        // dump($fruits);die;
        // dd($firstNameController);


        return $this->render('home/home.html.twig', [
            // k => v
            // "k" sera le nom de la variable en twig
            // "v" sera le nom de la variable de cette méthode
            'firstNameTwig' => $firstNameController,
            'fruits' => $fruits
        ]);
    }
    



} // Ne rien écrire en dessous de la classe



?>
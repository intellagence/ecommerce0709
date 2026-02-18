<?php

namespace App\Controller; // App = le dossier src

// Toutes les class dans le fichier doivent être importées

use App\Entity\Comment;
use App\Entity\Product;

use App\Form\CommentType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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
    public function catalog(ProductRepository $productRepository): Response
    {
        // dd('ok'); // dump die

        /* 
            Cette route doit retourner une vue html donc la méthode de la route va utiliser le terme "return" et employer la méthode render() provenant de la class héritée AbstractController

            Cette méthode permet de rendre une vue provenant du dossier templates et elle se déplace directement à la racine de ce dernier
            Cette méthode a besoin de 2 arguments
            - 1e OBLIGATOIRE (type : string) : nom du fichier ainsi que son emplacement dans le dossier templates
            - 2e FACULTATIF (type : array) : tableau des données
        */
        return $this->render('home/catalog.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }

    #[Route('/catalogue/produit/{id}', name:'app_catalog_product')]
    public function catalogProduct(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {

        $comment = new Comment();
        
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setProduct($product);
            $comment->setUser($this->getUser());
            
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a bien été ajouté');

            return $this->redirectToRoute('app_catalog_product', ['id' => $product->getId()]);

        }

        return $this->render('home/catalog_product.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
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
            'fruits' => $fruits,
        ]);
    }



    #[Route('/chart', name:'app_chart')]
    public function chart(ChartBuilderInterface $chartBuilder): Response
    {


        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'Cookies eaten 🍪',
                    'backgroundColor' => 'rgb(255, 99, 132, .4)',
                    'borderColor' => 'rgb(97, 17, 202)',
                    'data' => [2, 10, 50, 18, 20, 30, 45],
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Km walked 🏃‍♀️',
                    'backgroundColor' => 'rgba(244, 175, 0, 0.4)',
                    'borderColor' => 'rgba(45, 220, 126)',
                    'data' => [10, 15, 4, 3, 25, 841, 125],
                    'tension' => 0.4,
                ],
            ],
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
        ]);


        return $this->render('home/chart.html.twig', [
            'chart' => $chart,
        ]);
    }
    



} // Ne rien écrire en dessous de la classe



?>
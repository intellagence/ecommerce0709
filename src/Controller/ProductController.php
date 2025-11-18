<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/product')]
final class ProductController extends AbstractController
{
    
    #[Route('', name:'app_product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        /*
            Lorsqu'on a créé la class (entity) Product, la class ProductRepository a été générée,
            Le repository permet de faire des requêtes SELECT (uniquement)

            Il existe des méthodes prédéfinies dans tous les repository
                - findAll() => SELECT * FROM product (return array)
                - find($arg) => SELECT * FROM produit WHERE id = $arg
                - findBy() 
                - findOneBy()

        */

        $products = $productRepository->findAll();

        //dd($products);

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/new', name:'app_product_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'un objet issu de la class (Entity) Product
        // C'est dans celui-ci qu'on récupérera les données du formulaire et qu'on les injectera en BDD
        $product = new Product();
        dump($product);

        /*
            Pour créer un formulaire, on utilise la méthode createForm() provenant de la class héritée AbstractController

            le 1e argument est le nom de la class (Type) dans lequel se trouve le builder (plan de construction du formulaire), attention il est demandé le nom de la class, il faut rajouter après son nom "::class"

            le 2e argument est le nom de l'objet issu de la même entity que celle qui a permis de créer la classType

            La méthode createForm retourne un objet issu de la class FormInterface

            Donc ici $form est un objet
        */
        $form = $this->createForm(ProductType::class, $product);
        
        // traiment du formulaire 
        $form->handleRequest($request);

        /*
            Si le formulaire a été soumis
            et si le formulaire a été validé ( = respect des constraintes/ conditions)
        */
        if ($form->isSubmitted() && $form->isValid()) {
            dump($product);

            /*
                MVC :
                    - M : Model
                    - Entity = table
                    - Repository = Requête SELECT
                    - EntityManagerInterface = Requêtes INSERT INTO UPDATE DELETE

            */

            // Insérer le produit en base de données
            $entityManager->persist($product);
            $entityManager->flush();


            //dd($product);


            // Notification
            $this->addFlash('success', 'Le produit a bien été ajouté');


            // Redirection (équivalent à la fonction twig path())
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'formProduct' => $form->createView(),
            /*
                On passe à la vue de notre route, l'objet form (dans celui-ci se trouve la méthode permettant de créer la vue HTML du formulaire)

                Depuis la version 7, on n'est plus obligé de préciser le nom de la méthode ->createView()
            */
        ]);
    }

    #[Route('/show/{id}', name:'app_product_show')]
    public function show(Product $product): Response
    {
        /*
            On récupère le paramètre de l'URL dans la route, il s'écrit entre accolade et doit s'appeller comme dans la fonction twig path,

            Ici ce paramètre est une clé primaire, elle est injectée dans l'objet créé en dépendence, Doctrine se charge de récupérer l'intégralité des données provenant de la table
        */
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/edit/{id}', name:'app_product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('success', 'Le produit a bien été modifié');

            return $this->redirectToRoute('app_product_index');

        }
        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formProduct' => $form
        ]);
    }

    #[Route('/delete/{id}', name:'app_product_delete')]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit a bien été supprimé');

        return $this->redirectToRoute('app_product_index');
    }



}



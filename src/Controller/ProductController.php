<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
    #[Route('/product', name:'app_product_index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig');
    }


    #[Route('/product/new', name:'app_product_new')]
    public function new(): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        dump($product);


        return $this->render('product/new.html.twig', [
            'formProduct' => $form
        ]);
    }


}



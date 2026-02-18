<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/panier')]
final class CartController extends AbstractController
{

    public function __construct(private CartService $cartService)
    {
    }

    #[Route('', name: 'app_cart_index')]
    public function index(): Response
    {
        $cartProducts = [];

        $user = $this->getUser();
        if ($user && $user instanceof User) {
            foreach ($user->getCartItems() as $cartItem) {
                $cartProducts[] = [
                    'product' => $cartItem->getProduct(),
                    'quantity' => $cartItem->getQuantity()
                ];
            }
        } else {

            $cartProductsInSession = $this->cartService->getCart();

            foreach ($cartProductsInSession as $id => $quantity) {
                $cartProducts[] = [
                    'product' => $this->cartService->getProductById($id),
                    'quantity' => $quantity
                ];
            }

        }




        $account = $this->cartService->getAccountCart();

        return $this->render('cart/index.html.twig', [
            'cartProducts' => $cartProducts,
            'account' => $account
        ]);
    }



    #[Route('/ajouter', name: 'app_cart_add', methods: ['POST'])]
    public function add(Request $request): Response
    {

        $quantity = $request->request->get('quantity');
        $productId = $request->request->get('productId');

        // dump($quantity); dump($productId); dd($request);

        $this->cartService->addProduct($quantity, $productId);

        $this->addFlash('success', 'le produit a bien été ajouté au panier');

        return $this->redirectToRoute('app_cart_index');
    }



    #[Route('/vider', name: 'app_cart_clean', methods: ['POST'])]
    public function clean(): Response
    {
        $this->cartService->cleanCart();

        $this->addFlash('success', 'le panier est bien vidé');

        return $this->redirectToRoute('app_cart_index');
    }


    #[Route('/retirer-produit', name: 'app_cart_product_remove', methods: ['POST'])]
    public function removeProduct(Request $request): Response
    {
        $productId = $request->request->get('productId');

        $this->cartService->removeProduct($productId);

        $this->addFlash('success', 'le produit a bien été retiré du panier');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/ajuster-produit', name: 'app_cart_product_adjust', methods: ['POST'])]
    public function adjustProduct(Request $request): Response
    {
        $productId = $request->request->get('productId');
        $adjust = $request->request->get('adjust');

        $this->cartService->adjustProduct($productId, $adjust);

        $this->addFlash('success', 'la quantité du produit a bien été ajustée');

        return $this->redirectToRoute('app_cart_index');
    }




    
    

}

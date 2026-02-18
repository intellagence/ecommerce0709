<?php 

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    public function __construct(private RequestStack $requestStack, private ProductRepository $productRepository, private Security $security, private CartItemRepository $cartItemRepository, private EntityManagerInterface $entityManager)
    {
        
    }

    public function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }


    /*
        Si l'utilisateur est connecté : panier en BDD
        Si l'utilisateur n'est pas connecté : panier en session
    */


    /**
     * getCart() permet de récupérer le panier (ou de le créer)
     *
     * @return array
     */
    public function getCart(): array
    {
        // Récupérer le tableau 'cart' dans la session, (s'il n'existe pas on génère un tableau)
        return $this->getSession()->get('cart', []);
    }

    /**
     * addProduct() permet d'ajouter un produit et sa quantité dans le "panier"
     *
     * @param integer $quantity
     * @param integer $id
     * @return void
     */
    public function addProduct(int $quantity, int $id): void
    {

        $user = $this->security->getUser();

        if ($user) {
            
            $product = $this->getProductById($id);

            // je recherche un objet cartItem qui conserne le produit en question et l'utilisateur connecté
            $cartItemExisting = $this->cartItemRepository->findOneBy([
                'product' => $product,
                'user' => $user
            ]);

            if ($cartItemExisting) {
                $cartItemExisting->setQuantity($cartItemExisting->getQuantity() + $quantity);
            } else {
                $cartItem = new CartItem();
                $cartItem->setUser($user);
                $cartItem->setProduct($product);
                $cartItem->setQuantity($quantity);
                $this->entityManager->persist($cartItem);
            }
            $this->entityManager->flush();
            

        } else {

            $cart = $this->getCart();

            // Vérifier si l'id du produit existe déjà dans le tableau
            if (array_key_exists($id, $cart)) {
                $cart[$id] += $quantity;
            } else {
                $cart[$id] = $quantity;
            }
        
            // Enregistrer le tableau $cart dans la session
            $this->getSession()->set('cart', $cart);

        }

    }


    /**
     * cleanCart() détruit le panier
     *
     * @return void
     */
    public function cleanCart(): void
    {
        $user = $this->security->getUser();

        if ($user && $user instanceof User) {

            foreach ($user->getCartItems() as $cartItem) {
                $this->entityManager->remove($cartItem);
                $this->entityManager->flush();
            }

        } else {

            $this->getSession()->remove('cart');

        }
    }


    /**
     * Undocumented function
     *
     * @param integer $id
     * @return void
     */
    public function removeProduct(int $id): void
    {

        $user = $this->security->getUser();

        if ($user && $user instanceof User) {

            $product = $this->getProductById($id);

            $cartItem = $this->cartItemRepository->findOneBy([
                'product' => $product,
                'user' => $user
            ]);


            $this->entityManager->remove($cartItem);
            $this->entityManager->flush();

        } else {

            $cart = $this->getCart();

            unset($cart[$id]);

            $this->getSession()->set('cart', $cart);

        }
    }

    /**
     * getAccountCart()
     *
     * @return float
     */
    public function getAccountCart(): float
    {
        $account = 0;

        $user = $this->security->getUser();

        if ($user && $user instanceof User) {

            foreach ($user->getCartItems() as $cartItem) {
                $account += $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();
            }

        } else {

            $cart = $this->getCart();

            foreach ($cart as $id => $quantity) {
                $product = $this->getProductById($id);
                $account += $product->getPrice() * $quantity;
            }

        }

        return $account;
    }


    /**
     * getProductById() retourne un objet de Product dans la BDD à partir de son ID
     *
     * @param integer $id
     * @return Product|null
     */
    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }



    /**
     * getQuantityCart()
     *
     * @return int
     */
    public function getQuantityCart(): int
    {
        $quantityCart = 0;

        $user = $this->security->getUser();

        if ($user && $user instanceof User) {

            foreach ($user->getCartItems() as $cartItem) {
                $quantityCart += $cartItem->getQuantity();
            }

        } else {

            $cart = $this->getCart();

            foreach ($cart as $quantity) {
                $quantityCart += $quantity;
            }

        }

        return $quantityCart;
    }


    /**
     * Undocumented function
     *
     * @param integer $id
     * @param string $adjust
     * @return void
     */
    public function adjustProduct(int $id, string $adjust): void
    {

        $user = $this->security->getUser();

        if ($user && $user instanceof User) {

            $product = $this->getProductById($id);

            $cartItem = $this->cartItemRepository->findOneBy([
                'product' => $product,
                'user' => $user
            ]);


            if ($adjust == 'minus') {
                $cartItem->setQuantity($cartItem->getQuantity() - 1);
            } else {
                $cartItem->setQuantity($cartItem->getQuantity() + 1);
            }

            $this->entityManager->flush();

        } else {


            $cart = $this->getCart();

            if ($adjust == 'minus') {
                $cart[$id]--;
            } else {
                $cart[$id]++;
            }
            
            $this->getSession()->set('cart', $cart);

        }
    }




    /*
        - vérifier les quantités des stocks des produits
        - commander
    */



}
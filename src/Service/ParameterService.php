<?php

namespace App\Service;

class ParameterService 
{

    public function __construct(private CartService $cartService)
    {

    }


    public function getCartQuantity(): int
    {
        return $this->cartService->getQuantityCart();
    }

}
<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Repository\ProductRepository;

readonly class CartView
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function toArray(Cart $cart): array
    {
        $data = [
            'uuid' => $cart->getUuid(),
            'customer' => [
                'id' => $cart->getCustomer()->getId(),
                'name' => implode(' ', [
                    $cart->getCustomer()->getLastName(),
                    $cart->getCustomer()->getFirstName(),
                    // А вот бывают и без отчеств люди, надо это учитывать при проектировании таблиц
                    // Но раз сущность вернет string, то ладно
                    $cart->getCustomer()->getMiddleName(),
                ]),
                'email' => $cart->getCustomer()->getEmail(),
            ],
            'payment_method' => $cart->getPaymentMethod(),
        ];

        $total = 0;
        $data['items'] = [];

        foreach ($cart->getItems() as $item) {
            $itemTotalPrice = $item->getPrice() * $item->getQuantity();
            $total += $itemTotalPrice;
            // тут конечно каждый раз БД дергать некрасиво. Эти категории у товаров будут постоянно повторяться
            // в идеале добавить foreach выше и узнать все уникальные категории товаров в корзине
            // Отдельно у БД запросить все нужные категории и в массив положит эти категории товаров. Потом в этом форыче брать нужную категорию из сформированного массива 
            $product = $this->productRepository->getByUuid($item->getProductUuid());

            $data['items'][] = [
                'uuid' => $item->getUuid(),
                'price' => $item->getPrice(),
                // переназвать бы total лучше в sum или что то подобное
                'total' => $itemTotalPrice,
                'quantity' => $item->getQuantity(),
                'product' => [
                    'id' => $product->getId(),
                    'uuid' => $product->getUuid(),
                    'name' => $product->getName(),
                    'thumbnail' => $product->getThumbnail(),
                    'price' => $product->getPrice(),
                ],
            ];
        }

        $data['total'] = $total;

        return $data;
    }
}

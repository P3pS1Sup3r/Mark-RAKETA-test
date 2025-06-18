<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Repository;

use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Infrastructure\ConnectorFacade;

class CartManager extends ConnectorFacade
{
    public function __construct($host, $port, $password, private $logger)
    {
        parent::__construct($host, $port, $password, 1);
        parent::build();
    }

    /**
     * @inheritdoc
     */
    public function saveCart(Cart $cart)
    {
        try {
            // По дефолту сессия храниться 24 минуты, а не 24 часа
            // И вообще мне не нравиться идея хранить ключ к корзине в php сессии, это черевато проблеммами с производительностью и безопасностью
            // Лучше уж создать куку через уникальный токен на 24 часа и обращаться к ней
            // Ну и ещё надо бы получать корзину для авторизованных пользователей через их user_id, и хранить такую корзину в БД. Но тут я наверное слишком глубоко копаю для тестового задания.
            $this->connector->set(session_id(), $cart);
        } catch (Exception $e) {
            $this->logger->error('Ошибка при сохранении корзины', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'id' => session_id(),
            ]);
        }
    }

    /**
     * @return ?Cart
     */
    public function getCart()
    {
        try {
            // По дефолту сессия храниться 24 минуты, а не 24 часа
            // И вообще мне не нравиться идея хранить ключ к корзине в php сессии, это черевато проблеммами с производительностью и безопасностью
            // Лучше уж создать куку через уникальный токен на 24 часа и обращаться к ней
            // Ну и ещё надо бы получать корзину для авторизованных пользователей через их user_id, и хранить такую корзину в БД. Но тут я наверное слишком глубоко копаю для тестового задания.
            return $this->connector->get(session_id());
        } catch (Exception $e) {
            $this->logger->error('Ошибка при получении корзины', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'id' => session_id(),
            ]);
        }

        return new Cart(session_id(), []);
    }
}

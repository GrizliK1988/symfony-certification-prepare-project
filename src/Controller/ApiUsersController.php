<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 19.10.15
 * Time: 21:08
 */

namespace DG\SymfonyCert\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApiUsersController extends Controller
{
    public function getAction()
    {
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->get('token_storage');
        /** @var TokenInterface $token */
        $token = $tokenStorage->getToken();

        return new JsonResponse([
            ['currentUser' => $token->getUsername()],
            ['username' => 'admin'],
            ['username' => 'test'],
            ['username' => 'test2'],
            ['username' => 'test3'],
        ]);
    }

    public function adminAction()
    {
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->get('token_storage');
        /** @var TokenInterface $token */
        $token = $tokenStorage->getToken();

        return new JsonResponse([
            'currentUser' => $token->getUser()
        ]);
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.10.15
 * Time: 20:29
 */

namespace DG\SymfonyCert\Service\Security\Authentication\Token;


use DG\SymfonyCert\Entity\ApiUser;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class ApiUserToken extends AbstractToken
{
    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
    }
}
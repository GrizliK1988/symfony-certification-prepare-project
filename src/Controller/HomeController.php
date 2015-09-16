<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 15.09.15
 * Time: 21:34
 */

namespace DG\SymfonyCert\Controller;

use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return new Response('Hello!');
    }
} 
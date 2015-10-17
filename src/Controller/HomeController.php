<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 15.09.15
 * Time: 21:34
 */

namespace DG\SymfonyCert\Controller;

use Symfony\Component\HttpFoundation\Request;
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

    public function helloAction(Request $request)
    {
        var_dump($request->attributes->all());

        return new Response(sprintf('Hello, %s!', $request->attributes->get('data')['name']));
    }

    public function xmlAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/xml');
        $response->setContent('<Test><Message>Hello!</Message></Test>');
        return $response;
    }
} 
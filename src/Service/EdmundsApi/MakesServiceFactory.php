<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 27.09.15
 * Time: 11:50
 */

namespace DG\SymfonyCert\Service\EdmundsApi;


use DG\SymfonyCert\Service\Serializer\DelegatingSerializer;

class MakesServiceFactory
{
    public function createService($apiEndpoint, $apiKey, DelegatingSerializer $delegatingSerializer)
    {
        $service = new MakesService($apiEndpoint, $apiKey, $delegatingSerializer);
        return $service;
    }
} 
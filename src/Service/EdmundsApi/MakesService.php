<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 20.09.15
 * Time: 12:12
 */

namespace DG\SymfonyCert\Service\EdmundsApi;


use GuzzleHttp;
use Psr\Http\Message\ResponseInterface;

class MakesService
{
    const API_METHOD = 'makes';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiEndpoint;

    function __construct($apiEndpoint, $apiKey)
    {
        $this->apiEndpoint = $apiEndpoint;
        $this->apiKey = $apiKey;
    }

    public function getMakes($state, $year, $view = 'full')
    {
        $client = new GuzzleHttp\Client();

        $query = GuzzleHttp\Psr7\build_query([
            'api_key' => $this->apiKey,
            'state' => $state,
            'year' => $year,
            'view' => $view,
            'fmt' => 'json'
        ]);

        /** @var ResponseInterface $result */
        $result = $client->request('GET', $this->apiEndpoint . self::API_METHOD . '?' . $query);

        return (string)$result->getBody();
    }
} 
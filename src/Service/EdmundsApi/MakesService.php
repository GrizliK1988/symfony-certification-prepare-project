<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 20.09.15
 * Time: 12:12
 */

namespace DG\SymfonyCert\Service\EdmundsApi;


use DG\SymfonyCert\Event\ApiCallEvent;
use DG\SymfonyCert\Service\Serializer\DelegatingSerializer;
use GuzzleHttp;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\GenericEvent;

class MakesService extends BaseApiService
{
    const EVENT_CACHE_COMPLETE = 'makes.cache.complete';
    const DIC_SERVICE = 'api.makes';
    const API_METHOD = 'makes';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiEndpoint;

    /**
     * @var DelegatingSerializer
     */
    private $delegatingSerializer;

    function __construct($apiEndpoint, $apiKey, DelegatingSerializer $delegatingSerializer)
    {
        $this->apiEndpoint = $apiEndpoint;
        $this->apiKey = $apiKey;
        $this->delegatingSerializer = $delegatingSerializer;
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

        if ($this->logger)
            $this->logger->log(LogLevel::INFO, sprintf('Makes response : %s', $result->getBody()));

        if ($this->dispatcher)
            $this->dispatcher->dispatch(ApiCallEvent::EVENT_NAME, new GenericEvent($this, ['state' => $state, 'year' => $year, 'view' => $view]));

        return $this->delegatingSerializer->deserialize((string)$result->getBody(), 'json', ['type' => 'array']);
    }
}
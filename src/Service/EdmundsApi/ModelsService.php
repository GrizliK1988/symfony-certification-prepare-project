<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 30.09.15
 * Time: 20:33
 */

namespace DG\SymfonyCert\Service\EdmundsApi;


use DG\SymfonyCert\Event\ApiCallEvent;
use DG\SymfonyCert\Event\MakesCacheEvent;
use DG\SymfonyCert\Service\Serializer\DelegatingSerializer;
use GuzzleHttp;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModelsService extends BaseApiService
{
    const DIC_SERVICE = 'api.models';
    const API_METHOD = 'models';

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

    public function onMakesCache(MakesCacheEvent $event, $name, EventDispatcherInterface $dispatcher)
    {
//        print "Caught " . $name . " event" . PHP_EOL;
    }

    public function getModel($make, $model, array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'year' => 1990,
            'view' => 'basic'
        ]);

        $resolver->setDefined('state');
        $resolver->setRequired(['state', 'view']);
        $resolver->setAllowedTypes('year', 'integer');
        $resolver->setAllowedValues('state', ['new', 'used']);
        $resolver->setDefault('state', function (Options $options, $previousValue) {
            if ($options['year'] >= 2015) {
                return 'new';
            }

            return $previousValue;
        });

//        $resolver->isDefined('state');

        $options = $resolver->resolve($options);

        $client = new GuzzleHttp\Client();

        $queryParams = array_merge($options, ['fmt' => 'json', 'api_key' => $this->apiKey]);
        $query = GuzzleHttp\Psr7\build_query($queryParams);
        $request = new GuzzleHttp\Psr7\Request('GET', $this->apiEndpoint . $make . '/' . $model . '?' . $query);

        /** @var ResponseInterface $response */
        $response = $client->send($request);

        if ($this->dispatcher)
            $this->dispatcher->dispatch(ApiCallEvent::EVENT_NAME, new GenericEvent($this, ['make' => $make, 'model' => $model, 'options' => $options]));

        return $this->delegatingSerializer->deserialize($response->getBody(), 'json', ['type' => 'array']);
    }
} 
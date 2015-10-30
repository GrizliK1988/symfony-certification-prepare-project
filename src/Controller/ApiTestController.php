<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 19.10.15
 * Time: 21:08
 */

namespace DG\SymfonyCert\Controller;


use DG\SymfonyCert\Entity\TestEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiTestController extends Controller
{
    public function getAction()
    {
        $normalizer = new ObjectNormalizer(new ClassMetadataFactory(new YamlFileLoader(CONFIG_PATH . 'test_entity.yml')), new CamelCaseToSnakeCaseNameConverter());
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);

        $test = new TestEntity();
        $test->setMessage('Test');

        $test = $serializer->deserialize('<test><id>1</id></test>', 'DG\SymfonyCert\Entity\TestEntity', 'xml', [
            'object_to_populate' => $test
        ]);

        return new Response($serializer->serialize($test, 'xml', [
            'groups' => ['group2']
        ]), 200, ['Content-Type' => 'text/xml']);
    }
}
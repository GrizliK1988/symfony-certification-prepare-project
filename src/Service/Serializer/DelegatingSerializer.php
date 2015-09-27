<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 25.09.15
 * Time: 21:36
 */

namespace DG\SymfonyCert\Service\Serializer;


class DelegatingSerializer
{
    /**
     * @var SerializerInterface[]
     */
    private $serializers;

    public function __construct()
    {
        $this->serializers = [];
    }

    public function serialize($data, $format)
    {
        foreach ($this->serializers as &$serializer) {
            if ($serializer->supportsSerialization($data, $format)) {
                return $serializer->serialize($data);
            }
        }

        throw new \Exception('Serializer not found');
    }

    public function deserialize($serializedData, $format, array $options = [])
    {
        foreach ($this->serializers as &$serializer) {
            if ($serializer->supportsDeserialization($format, $options)) {
                return $serializer->deserialize($serializedData, $options);
            }
        }

        throw new \Exception('Serializer not found');
    }

    public function addSerializer(SerializerInterface $serializer, $format)
    {
        $this->serializers[$format] = $serializer;
    }
}
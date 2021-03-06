<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 25.09.15
 * Time: 21:29
 */

namespace DG\SymfonyCert\Service\Serializer;


class JsonToStdClassSerializer implements SerializerInterface
{
    public function serialize($data)
    {
        return json_encode($data);
    }

    public function deserialize($serializedData, array $options = [])
    {
        return json_decode($serializedData);
    }

    public function supportsSerialization($data, $format)
    {
        return $data instanceof \stdClass && $format === 'json';
    }

    public function supportsDeserialization($format, array $options = [])
    {
        return $format === 'json' && isset($options['type']) && $options['type'] === 'stdClass';
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 25.09.15
 * Time: 21:27
 */

namespace DG\SymfonyCert\Service\Serializer;


interface SerializerInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function serialize($data);

    /**
     * @param $serializedData
     * @param array $options
     * @return mixed
     */
    public function deserialize($serializedData, array $options = []);

    /**
     * @param $data
     * @param $format
     * @return bool
     */
    public function supportsSerialization($data, $format);

    /**
     * @param $format
     * @param array $options
     * @return bool
     */
    public function supportsDeserialization($format, array $options = []);
} 
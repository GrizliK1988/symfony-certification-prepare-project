<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.09.15
 * Time: 9:53
 */

namespace DG\SymfonyCert\Service;


use Symfony\Component\HttpFoundation\ParameterBag;

class ConfigBag extends ParameterBag
{
    public function reset(array $parameters)
    {
        $this->parameters = $parameters;
    }
} 
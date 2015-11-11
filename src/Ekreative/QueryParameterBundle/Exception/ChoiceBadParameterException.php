<?php

namespace Ekreative\QueryParameterBundle\Exception;

class ChoiceBadParameterException extends \Exception
{
    public function __construct($parameterName)
    {
        parent::__construct(sprintf('Fail value of the required query parameter  %s.', $parameterName), 400);
    }
}

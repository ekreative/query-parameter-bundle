<?php

declare(strict_types=1);

namespace Ekreative\QueryParameterBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ChoiceBadParameterException extends BadRequestHttpException
{
    public function __construct($parameterName)
    {
        parent::__construct(sprintf('Fail value of the required query parameter  %s.', $parameterName));
    }
}

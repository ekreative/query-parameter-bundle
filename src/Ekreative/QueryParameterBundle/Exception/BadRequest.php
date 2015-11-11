<?php

namespace Ekreative\QueryParameterBundle\Exception;

class BadRequest extends \Exception
{
    public function __construct($message)
    {
        parent::__construct(sprintf('Fail value of the required query parameter  %s.', $message), 400);
    }
}

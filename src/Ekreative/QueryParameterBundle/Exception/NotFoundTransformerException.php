<?php

declare(strict_types=1);

namespace Ekreative\QueryParameterBundle\Exception;

class NotFoundTransformerException extends \Exception
{
    public function __construct($transformerName)
    {
        parent::__construct(sprintf('Not found transformer for %s.', $transformerName), 500);
    }
}

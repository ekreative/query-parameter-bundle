<?php

namespace Ekreative\QueryParameterBundle\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer as ParentTransformer;

/**
 * Transforms between a Boolean and a string.
 *
 * @author AlexMoshta <ahonymous@gmail.com>
 */
class BooleanToStringTransformer extends ParentTransformer
{
    /**
     * Transforms a string into a Boolean.
     *
     * @param string $value String value.
     *
     * @return bool Boolean value.
     *
     * @throws TransformationFailedException If the given value is not a string.
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return false;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        if ("0" == $value || "false" === $value) {
            return false;
        }

        if ("1" == $value || "true" === $value) {
            return true;
        }

        throw new TransformationFailedException();
    }
}

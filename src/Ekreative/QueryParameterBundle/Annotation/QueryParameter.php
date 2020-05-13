<?php

declare(strict_types=1);

namespace Ekreative\QueryParameterBundle\Annotation;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The QueryParameter class handles the query string.
 *
 * @author Alex Moshta <ahonymous@gmail.com>
 *
 * @Annotation
 */
class QueryParameter extends QueryAnnotation
{
    /**
     * Default values for parameter options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [],
        ]);
    }
}

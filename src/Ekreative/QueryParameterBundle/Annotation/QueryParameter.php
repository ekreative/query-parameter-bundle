<?php

namespace Ekreative\QueryParameterBundle\Annotation;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The QueryParameter class handles the query string.
 *
 * @package AppBundle\Annotation
 * @author Alex Moshta <ahonymous@gmail.com>
 *
 * @Annotation
 */
class QueryParameter extends QueryAnnotation
{
    /**
     * Default values for parameter options.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [],
        ]);
    }
}

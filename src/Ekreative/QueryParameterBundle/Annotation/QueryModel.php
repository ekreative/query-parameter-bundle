<?php

declare(strict_types=1);

namespace Ekreative\QueryParameterBundle\Annotation;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The QueryModel class handles the query string as object.
 *
 * @author Alex Moshta <ahonymous@gmail.com>
 * @Annotation
 */
class QueryModel extends QueryAnnotation
{
    /**
     * The parameter class.
     *
     * @var string
     */
    protected $class;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'types' => [],
        ]);
    }
}

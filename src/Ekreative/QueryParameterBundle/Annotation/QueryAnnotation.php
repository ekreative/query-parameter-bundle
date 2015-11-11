<?php

namespace Ekreative\QueryParameterBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The QueryAnnotation class handles the query string.
 *
 * @package AppBundle\Annotation
 * @author Alex Moshta <ahonymous@gmail.com>
 *
 * @Annotation
 */
abstract class QueryAnnotation implements ConfigurationInterface
{
    /**
     * The parameter name of the query parameter.
     *
     * @var string
     */
    protected $name;

    /**
     * The parameter typeof the query parameter.
     *
     * @var string
     */
    protected $type;

    /**
     * The parameter options of the query parameter.
     *
     * @var array
     */
    protected $options;

    /**
     * QueryAnnotation constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $k => $v) {
            if (!method_exists($this, $name = 'set' . $k)) {
                throw new \RuntimeException(sprintf('Unknown key "%s" for annotation "@%s".', $k, get_class($this)));
            }

            $this->$name($v);
        }

        if (!$this->getOptions()) {
            $this->setOptions([]);
        }

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->setOptions(
            $resolver->resolve($this->getOptions())
        );
    }

    /**
     * Getter for parameter options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Setter for parameter options.
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Default values for parameter options.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
        ]);
    }

    /**
     * Getter for parameter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter for parameter name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Sets the parameter name.
     *
     * @param string $name The parameter name
     */
    public function setValue($name)
    {
        $this->setName($name);
    }

    /**
     * Getter for parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Setter for parameter type.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the alias name for an annotated configuration.
     *
     * @return string
     */
    public function getAliasName()
    {
        return 'queries';
    }

    /**
     * Returns whether multiple annotations of this type are allowed.
     *
     * @return bool
     */
    public function allowArray()
    {
        return true;
    }
}

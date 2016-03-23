<?php

namespace Ekreative\QueryParameterBundle\Manager;

use Doctrine\Common\Annotations\AnnotationException;
use Ekreative\QueryParameterBundle\Annotation\QueryModel;
use Ekreative\QueryParameterBundle\Annotation\QueryParameter;
use Ekreative\QueryParameterBundle\DataTransformer\BooleanToStringTransformer;
use Ekreative\QueryParameterBundle\Exception\BadRequest;
use Ekreative\QueryParameterBundle\Exception\ChoiceBadParameterException;
use Ekreative\QueryParameterBundle\Exception\NotFoundTransformerException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class QueryManager
 * @package AppBundle\Manager
 */
class QueryManager
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var array
     */
    private $transformers;

    public function __construct()
    {
        $this->transformers = [
            'integer' => $this->getIntTransformer(),
            'datetime' => $this->getDateTimeTransformer(),
            'boolean' => $this->getBoolTransformer(),
            'double' => $this->getFloatTransformer(),
        ];
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param array $configurations
     *
     * @return Request
     * @throws AnnotationException
     */
    public function manage(Request $request, array $configurations)
    {
        $parametersName = [];

        foreach ($configurations as $configuration) {
            if ($configuration instanceof QueryModel) {
                $this->manageQueryModel($request, $configuration);
            }

            if ($configuration instanceof QueryParameter) {
                $this->manageQueryParameter($request, $configuration);
                $parametersName[] = $configuration->getName();
            }
        }
        if (count($parametersName) > 0) {
            $this->checkParameters($parametersName, array_keys($request->query->all()));
        }

        return $request;
    }

    /**
     * @param array $params
     * @param array $requestParams
     *
     * @throws BadRequest
     */
    private function checkParameters(array $params, array $requestParams)
    {
        foreach ($requestParams as $param) {
            if (!in_array($param, $params)) {
                throw new BadRequest(sprintf('Query parameter `%s` is not defined', $param));
            }
        }
    }

    /**
     * @param Request $request
     * @param QueryModel $configuration
     *
     * @return Request
     * @throws BadRequest
     * @throws NotFoundTransformerException
     */
    protected function manageQueryModel(Request $request, QueryModel $configuration)
    {
        $className = $configuration->getClass();
        $types = $configuration->getOptions()['types'];
        $filter = new $className();
        $filterClass = new \ReflectionClass(get_class($filter));

        $options = [];
        /** @var \ReflectionProperty $property */
        foreach ($filterClass->getProperties() as $property) {
            $property->setAccessible(true);
            $options[$property->getName()] = $property->getValue($filter);
        }
        $resolver = new OptionsResolver();
        $resolver->setDefaults($options);

        $parameters = $request->query->all();
        foreach ($types as $k => $type) {
            $queryParam = $request->query->get($k);
            if (!is_null($queryParam)) {
                $transformer = $this->getTransformer($type);
                $parameters[$k] = $transformer->reverseTransform(false == $queryParam ? null : $queryParam);
                $resolver->setAllowedTypes($k, $type);
            }
        }

        $queries = $resolver->resolve($parameters);
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($queries as $k => $v) {
            if (!$accessor->isWritable($filter, $k)) {
                throw new \RuntimeException(sprintf('Unknown key "%s" for filter "@%s".', $k, get_class($this)));
            }

            $accessor->setValue($filter, $k, $v);
        }

        $errors = $this->getValidator()->validate($filter);
        if (count($errors) > 0) {
            throw new BadRequest('Bad query filter options.');
        }

        $request->attributes->set($configuration->getName(), $filter);

        return $request;
    }

    /**
     * @param Request $request
     * @param QueryParameter $configuration
     *
     * @return Request
     * @throws BadRequest
     * @throws ChoiceBadParameterException
     * @throws NotFoundTransformerException
     */
    protected function manageQueryParameter(Request $request, QueryParameter $configuration)
    {
        $parameterName = $configuration->getName();

        $parameter = $request->query->get($parameterName);

        if (is_null($parameter)) {
            if ($configuration->getOptions()['required']) {
                throw new BadRequest(sprintf('Missing query parameter %s.', $parameterName));
            }
            $request->attributes->set($parameterName, $parameter);

            return $request;
        }

        if ($configuration->getType() != 'choice') {
            $transformer = $this->getTransformer($configuration->getType());
            $transformedParameter = $transformer->reverseTransform($parameter);

            $request->attributes->set($parameterName, $transformedParameter);
        } elseif (!in_array($parameter, $configuration->getOptions()['choices'])) {
            throw new ChoiceBadParameterException($parameterName);
        }

        return $request;
    }


    /**
     * @param $name
     *
     * @return DataTransformerInterface
     * @throws NotFoundTransformerException
     */
    protected function getTransformer($name)
    {
        if (!array_key_exists($name, $this->transformers)) {
            throw new NotFoundTransformerException($name);
        }

        return $this->transformers[$name];
    }

    /**
     * @return IntegerToLocalizedStringTransformer
     */
    protected function getIntTransformer()
    {
        return new IntegerToLocalizedStringTransformer();
    }

    /**
     * @return DateTimeToStringTransformer
     */
    protected function getDateTimeTransformer()
    {
        return new DateTimeToStringTransformer();
    }

    /**
     * @return BooleanToStringTransformer
     */
    protected function getBoolTransformer()
    {
        return new BooleanToStringTransformer(true);
    }

    /**
     * @return NumberToLocalizedStringTransformer
     */
    protected function getFloatTransformer()
    {
        return new NumberToLocalizedStringTransformer();
    }
}
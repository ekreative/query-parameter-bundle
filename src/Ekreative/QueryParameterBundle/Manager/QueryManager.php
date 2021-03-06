<?php

declare(strict_types=1);

namespace Ekreative\QueryParameterBundle\Manager;

use Ekreative\QueryParameterBundle\Annotation\QueryModel;
use Ekreative\QueryParameterBundle\Annotation\QueryParameter;
use Ekreative\QueryParameterBundle\DataTransformer\BooleanToStringTransformer;
use Ekreative\QueryParameterBundle\Exception\ChoiceBadParameterException;
use Ekreative\QueryParameterBundle\Exception\NotFoundTransformerException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        $int = new IntegerToLocalizedStringTransformer();
        $bool = new BooleanToStringTransformer('1', ['false', '0']);
        $double = new NumberToLocalizedStringTransformer();
        $this->transformers = [
            'integer' => $int,
            'int' => $int,
            'datetime' => new DateTimeToStringTransformer(null, null, \DateTime::ISO8601),
            'boolean' => $bool,
            'bool' => $bool,
            'double' => $double,
            'float' => $double,
            'string' => null,
        ];
    }

    /**
     * @return Request
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
        if (\count($parametersName) > 0) {
            $this->checkParameters($parametersName, array_keys($request->query->all()));
        }

        return $request;
    }

    /**
     * @throws BadRequestHttpException
     */
    private function checkParameters(array $params, array $requestParams)
    {
        foreach ($requestParams as $param) {
            if (!\in_array($param, $params)) {
                throw new BadRequestHttpException(sprintf('Query parameter `%s` is not defined', $param));
            }
        }
    }

    /**
     * @return Request
     *
     * @throws BadRequestHttpException
     * @throws NotFoundTransformerException
     */
    private function manageQueryModel(Request $request, QueryModel $configuration)
    {
        $className = $configuration->getClass();
        $types = $configuration->getOptions()['types'];
        $filter = new $className();
        $filterClass = new \ReflectionClass(\get_class($filter));

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
            if (null !== $queryParam) {
                $transformer = $this->getTransformer($type);
                if ($transformer) {
                    $parameters[$k] = $transformer->reverseTransform(false == $queryParam ? null : $queryParam);
                }
                $resolver->setAllowedTypes($k, $type);
            }
        }

        $queries = $resolver->resolve($parameters);
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($queries as $k => $v) {
            if (!$accessor->isWritable($filter, $k)) {
                throw new \RuntimeException(sprintf('Unknown key "%s" for filter "@%s".', $k, static::class));
            }

            $accessor->setValue($filter, $k, $v);
        }

        $errors = $this->validator->validate($filter);
        if (\count($errors) > 0) {
            throw new BadRequestHttpException('Bad query filter options.');
        }

        $request->attributes->set($configuration->getName(), $filter);

        return $request;
    }

    /**
     * @return Request
     *
     * @throws BadRequestHttpException
     * @throws ChoiceBadParameterException
     * @throws NotFoundTransformerException
     */
    private function manageQueryParameter(Request $request, QueryParameter $configuration)
    {
        $parameterName = $configuration->getName();

        $parameter = $request->query->get($parameterName);

        if (null === $parameter) {
            if ($configuration->getOptions()['required']) {
                throw new BadRequestHttpException(sprintf('Missing query parameter %s.', $parameterName));
            }
            $request->attributes->set($parameterName, $parameter);

            return $request;
        }

        if ($configuration->getType() != 'choice') {
            $transformer = $this->getTransformer($configuration->getType());
            if ($transformer) {
                $transformedParameter = $transformer->reverseTransform($parameter);
            } else {
                $transformedParameter = $parameter;
            }

            $request->attributes->set($parameterName, $transformedParameter);
        } elseif (!\in_array($parameter, $configuration->getOptions()['choices'])) {
            throw new ChoiceBadParameterException($parameterName);
        }

        return $request;
    }

    /**
     * @param $name
     *
     * @return DataTransformerInterface
     *
     * @throws NotFoundTransformerException
     */
    private function getTransformer($name)
    {
        if (!\array_key_exists($name, $this->transformers)) {
            throw new NotFoundTransformerException($name);
        }

        return $this->transformers[$name];
    }
}

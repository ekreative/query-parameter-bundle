<?php

declare(strict_types=1);

namespace Ekreative\QueryParameterBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Foo
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}

<?php

declare(strict_types=1);

namespace Ekreative\QueryParameterBundle\Controller;

use Ekreative\QueryParameterBundle\Annotation\QueryModel;
use Ekreative\QueryParameterBundle\Annotation\QueryParameter;
use Ekreative\QueryParameterBundle\Model\Foo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    /**
     * @Route("/param")
     * @QueryParameter("test", type="boolean", options={"required": true})
     */
    public function paramAction(bool $test)
    {
        return new Response($test ? 'true' : 'false', 200);
    }

    /**
     * @Route("/model")
     * @QueryModel("foo", class="Ekreative\QueryParameterBundle\Model\Foo", options={"required": true})
     */
    public function modelAction(Foo $foo)
    {
        return new Response($foo->getName());
    }
}

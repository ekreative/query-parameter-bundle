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
     * @Route("/param/bool")
     * @QueryParameter("test", type="bool", options={"required": true})
     */
    public function paramBoolAction(bool $test)
    {
        return new Response($test ? 'true' : 'false', 200);
    }

    /**
     * @Route("/param/int")
     * @QueryParameter("test", type="int", options={"required": true})
     */
    public function paramIntAction(int $test)
    {
        return new Response("$test", 200);
    }

    /**
     * @Route("/param/string")
     * @QueryParameter("test", type="string", options={"required": true})
     */
    public function paramStringAction(string $test)
    {
        return new Response($test, 200);
    }

    /**
     * @Route("/param/date")
     * @QueryParameter("test", type="datetime", options={"required": true})
     */
    public function paramDateAction(\DateTime $test)
    {
        return new Response($test->format('c'), 200);
    }

    /**
     * @Route("/param/float")
     * @QueryParameter("test", type="float", options={"required": true})
     */
    public function paramFloatAction(float $test)
    {
        return new Response("$test", 200);
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

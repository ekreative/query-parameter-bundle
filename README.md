Query parameter bundle
======================

The Symfony Bundle for validation request query string parameters in the controllers.

## Instaition

    composer require ekreative/query-parameter-bundle

## Requirements

The Bundle required `sensio/framework-extra-bundle`, Symfony components `OptionResolver` and `PropertyAccess`.

## Configuring

    app/AppKernel.php
    
    ...
        public function registerBundles()
        {
            $bundles = array(
                ...
                new Ekreative\QueryParameterBundle\EkreativeQueryParameterBundle(),
                ...
            );
            ...
            
            return $bundles;
        }
    ...


## Examples

### QueryParameter

    src/AppBundle/Controller/DefaultController.php
    
    ...
    /**
     * @Route("/")
     * @QueryParameter("test", type="boolean", options={"required" = false})
     */
    public function indexAction($test)
    ...


### QueryModel

    src/AppBundle/Controller/DefaultController.php
    
    ...
    /**
     * @Route("/")
     * @QueryModel("testFilter", class="AppBundle\Filter\Filter", options={"required" = false, types={"test" = "boolean"}})
     */
    public function indexAction(Filter $testFilter)
    ...

## Variable types

* integer
* datetime
* double
* boolean

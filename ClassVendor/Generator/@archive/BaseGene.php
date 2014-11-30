<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class BaseGene {

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    protected $kernel;
    protected $results;

    public function __construct($container, Kernel $kernel, Router $router)
    {
        $this->container = $container;
        $this->kernel = $kernel;
        $this->router = $router;
//        $url = $this->container->get('router')->generate('_configurator_step', array('index' => 0));
    }

    /**
     *
     * @param type $options ['dir_root'] => /src/
     */
    public function getBaseDefaut($namespace, $dir_src, $prefix_table = false)
    {
        $bdd_register = new Register($this->kernel, $this->router);
        $bdd_register->setConnect(
        $this->container->getParameter('database_host'), $this->container->getParameter('database_user'), $this->container->getParameter('database_password'), $this->container->getParameter('database_name')
        );
        $bdd = $bdd_register->getEntityGenerator();

        $bdd->setConfig($namespace, $dir_src, $prefix_table);
        $bdd->dispatch(true);

        $this->setResult($this->container->getParameter('database_name'), $bdd);
    }

    protected function setResult($basename, $bdd)
    {
        $this->results[$basename] = $bdd;
    }

    public function getResults()
    {
        return $this->results;
    }

}

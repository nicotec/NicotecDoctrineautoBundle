<?php

namespace WsGene\EditBundle\ClassVendor\Generator\Entity;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Kernel;
use WsGene\EditBundle\ClassVendor\Generator\Entity\EntityGenerator;
use WsGene\EditBundle\ClassVendor\Generator\Register;

/**
 * Gestion de la création et de l'affichage du générateur de doctrine2
 */
class EntityRegister {

    protected $container;
    protected $kernel;
    protected $results;
    protected $namespaceBundle;

    public function __construct(Container $container, Kernel $kernel, Router $router, $namespaceBundle)
    {
        $this->container = $container;
        $this->kernel = $kernel;
        $this->router = $router;
        $this->namespaceBundle = $namespaceBundle;
    }

    public function setNamespaceBundle($namespaceBundle)
    {
        $this->namespaceBundle = $namespaceBundle;
    }

    public function generate($database_name, $database_host, $database_user, $database_password)
    {
        $bdd_register = new Register($this->kernel, $this->router);
        $bdd_register->setConnect($database_name, $database_host, $database_user, $database_password);
        $bdd_register->setShowTable();

        $eg = new EntityGenerator($bdd_register);
        $eg->setConfig($this->namespaceBundle);
        $eg->dispatch(true);

        $this->setResult($this->container->getParameter('database_name'), $eg);
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

<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Doctrine;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Router;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Entity\EntityGenerator;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Register;

class DoctrineRegister {

    protected $container;
    protected $kernel;
    protected $request;
    protected $results;
    protected $namespace_bundle_entity;
    protected $namespace_bundle_controller;
    protected $contoller;
    protected $action;
    protected $table;
    protected $bilan;
    protected $gene;

    public function __construct(Request $request, Container $container, Kernel $kernel, Router $router)
    {
        $this->request = $request;
        $this->container = $container;
        $this->kernel = $kernel;
        $this->router = $router;
    }

    public function getGene()
    {
        return $this->gene;
    }

    public function getBilan()
    {
        return $this->bilan;
    }

    public function generate()
    {
        $bdd_register = new Register($this->kernel, $this->router);
        $bdd_register->setConnect(
        $this->container->getParameter('database_host'), $this->container->getParameter('database_user'), $this->container->getParameter('database_password'), $this->container->getParameter('database_name')
        );
        $bdd_register->setShowTable();

        $eg = new EntityGenerator($bdd_register);
        $eg->setConfig($this->getNamespaceBundleEntity());
        $eg->dispatch();

        $this->gene = $eg->getGene();
        $this->bilan = $bdd_register->getBilan();

        if($this->getTable()) {
            $q = new Query($this->request, $this->kernel, $this->getGene(), $this->getBilan());

            return array(
                'select' => $q->getDoctrineSelect(),
                'update' => $q->getDoctrineUpdate(),
                'delete' => $q->getDoctrineDeleteOne(),
                'delete_all' => $q->getDoctrineDeleteAll(),
                'pagination' => $q->getPagination(),
                'pagination_twig' => $q->getPaginationTwig(),
            );
        }
        else {
            return false;
        }
    }

}

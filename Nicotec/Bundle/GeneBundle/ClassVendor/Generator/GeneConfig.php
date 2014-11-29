<?php

namespace WsGene\EditBundle\ClassVendor\Generator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

class GeneConfig {

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var EntityManager
     */
    protected $em;
    protected $parameters,$mappings, $gene_config;

    public function __construct(ContainerInterface $container)
    {
        $this->kernel = $container->get('kernel');
        $this->parameters = $container->getParameter('generator');
        $this->mappings = $this->parameters['mappings'];
        $this->gene_config = $container->get('session')->get('gene.config');
        $this->em = $container->get('doctrine')->getManager($this->getConnexionName());
    }

    private function filtre($name)
    {
//        echo $this->getConnexionName() . '--' . $name;
        if(isset($this->mappings[$this->getConnexionName()][$name])) {
            return $this->mappings[$this->getConnexionName()][$name];
        }

        return false;
    }

    private function getRootDirSrc()
    {
        return $this->kernel->getRootDir() . '/../src/';
    }

    public function getEm()
    {
        return $this->em;
    }

    public function getDatebaseName()
    {
        return $this->em->getConnection()->getDatabase();
    }

    public function getDatebaseHost()
    {
        return $this->em->getConnection()->getHost();
    }

    public function getDatebaseUser()
    {
        return $this->em->getConnection()->getUsername();
    }

    public function getDatebasePassword()
    {
        return $this->em->getConnection()->getPassword();
    }

    public function getConnexionNameDefault()
    {
        return $this->parameters['default_mapping'];
    }

    public function getConnexionName()
    {
        return $this->gene_config['connexion_name'];
    }

    public function getTable()
    {
        return $this->gene_config['table'];
    }

    public function getControllerNamespace()
    {
        return $this->gene_config['controller_namespace'];
    }

    public function getControllerName()
    {
        return $this->gene_config['controller_name'];
    }

    public function getAction()
    {
        return $this->gene_config['action'];
    }

    public function getSuffixRoute()
    {
        return $this->filtre('suffix_route');
    }

    public function getBddNamespace()
    {
        return $this->filtre('bdd_namespace');
    }

    public function getNamespaceBundle()
    {
        return $this->filtre('namespace_bundle');
    }

    public function hasSuperclass()
    {
        if($this->filtre('superclass')) {
            return true;
        }

        return false;
    }

    public function hasSecurity()
    {
        if($this->filtre('security')) {
            return true;
        }

        return false;
    }

    public function getSecurityEntity()
    {
        return $this->mappings[$this->getConnexionName()]['security']['entity'];
    }

    public function getTableIgnores()
    {
        if($this->filtre('table_ignores')) {
            return $this->filtre('table_ignores');
        }

        return array();
    }

    public function getDirEntity()
    {
        return $this->getBddNamespace() . '\Entity';
    }

    public function getDirModel()
    {
        return $this->getBddNamespace() . '\Model';
    }

    public function getDirRepository()
    {
        return $this->getBddNamespace() . '\Repository';
    }

    public function getSrcDirEntity()
    {
        return str_replace('\\', '/', $this->getRootDirSrc() . $this->getDirEntity());
    }

    public function getSrcDirModel()
    {
        return str_replace('\\', '/', $this->getRootDirSrc() . $this->getDirModel());
    }

    public function getSrcDirRepository()
    {
        return str_replace('\\', '/', $this->getRootDirSrc() . $this->getDirRepository());
    }

}

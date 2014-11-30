<?php
//
//namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;
//
//use Symfony\Bundle\FrameworkBundle\Routing\Router;
//use Symfony\Component\DependencyInjection\Container;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpKernel\Kernel;
//use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Entity\EntityGenerator;
//use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGenerator;
//use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Register;
//use apc_exists;
//use apc_store;
//use apc_fetch;
//
///**
// * Gestion de la création et de l'affichage du générateur de doctrine2
// */
//class FormRegister {
//
//    protected $container;
//    protected $kernel;
//    protected $request;
//    protected $router;
//    protected $bilan;
//    protected $gene;
//
//    public function __construct(Request $request, Container $container, Kernel $kernel, Router $router)
//    {
//        $this->request = $request;
//        $this->container = $container;
//        $this->kernel = $kernel;
//        $this->router = $router;
//    }
//
//    public function getGene()
//    {
//        return $this->gene;
//    }
//
//    public function getBilan()
//    {
//        return $this->bilan;
//    }
//
//    public function generate()
//    {
////        if(apc_exists('gene') && apc_exists('bilan')) {
//            $bdd_register = new Register($this->kernel, $this->router);
//            $bdd_register->setConnect(
//                $this->container->getParameter('database_host'), $this->container->getParameter('database_user'), $this->container->getParameter('database_password'), $this->container->getParameter('database_name')
//            );
//            $bdd_register->setShowTable();
//
//            $eg = new EntityGenerator($bdd_register);
//            $eg->setConfig($this->getNamespaceBundleEntity());
//            $eg->dispatch();
//
//            $this->gene = $eg->getGene();
//            $this->bilan = $bdd_register->getBilan();
//
////            apc_store('gene', $this->getGene(), 20);
////            apc_store('bilan', $this->getBilan(), 20);
////        }
//
//        if($this->getTable()) {
//            $fg = new FormGenerator($this->request, $this->kernel, $this->getGene(), $this->getBilan());
////            $fg = new FormGenerator($this->request, $this->kernel, apc_fetch('gene'), apc_fetch('bilan'));
//
//            return array(
//                'form_action' => $fg->getFormAction(),
//                'form_action_factorise' => $fg->getFormActionFactorise(),
//                'form_action_factorise_ui' => $fg->getFormActionUi(),
//                'form_type' => $fg->getFormType(),
//                'form_handler' => $fg->getFormHandler(),
//                'twig_form' => $fg->getTwigForm(),
//                'twig_form_developpe' => $fg->getTwigFormDeveloppe(),
//            );
//        }
//        else {
//            return false;
//        }
//    }
//
//}
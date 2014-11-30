<?php

namespace Nicotec\DoctrineautoBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\GeneConfig;
use Nicotec\DoctrineautoBundle\Controller\ReController as Controller;

class SetupController extends Controller {

    /**
     * @Route("/setup", name="gene.setup")
     * @Template()
     */
    public function setupAction(Request $request)
    {
        $session = $request->getSession();
        $form_champs = array();
//        $session->clear();die;
        $generator = $this->container->getParameter('generator');

        $gene_config = new GeneConfig($this->container);


        //par defaut
        if(!$session->has('gene.config')) {
            $c = $this->getDoctrine()->getConnection($gene_config->getConnexionNameDefault());
//echo $gene_config->getConnexionNameDefault();
//            die;
            $r = array(
                'connexion_name' => $gene_config->getConnexionNameDefault(),
                'database_name' => $c->getDatabase(),
                'database_host' => $c->getHost(),
                'database_user' => $c->getUsername(),
                'database_password' => $c->getPassword(),
                'generator' => $generator,
            );

            $session->set('gene.config', $r);
        }


        $bdds = array();
        foreach($this->getDoctrine()->getConnections() as $name => $value) {
//            echo get_class($value) . '--';
//            die;
            if(!isset($generator['mappings'][$name]['bdd_namespace'])) {
                throw $this->createNotFoundException('La base suivante n est pas indiquée: ' . $name);
            }
            $bdds[$name] = $name . ' (' . $generator['mappings'][$name]['bdd_namespace'] . ') [' . $value->getDatabase() . ']';
        }


//        if(!$session->has('gene.config')) {
//            $request->setMethod('post');
//
//            $name_default = $gene_config->getConnexionNameDefault();
//
//            $form_champs = array(
//                'connexion_name' => $name_default,
//                'controller_namespace' => $generator['mappings'][$name_default],
////            'table' => $gene_config->getTable(),
////            'controller_name' => $gene_config->getControllerName(),
////            'action' => $gene_config->getAction(),
////            'suffix_route' => $gene_config->getSuffixRoute(),
//            );
//
//            var_dump($form_champs);
//        }
//        else {
//
//        }




        $fb = $this->createFormBuilder($form_champs);

        $fb->add('connexion_name', 'choice', array('label' => 'Base de données', 'choices' => $bdds, 'attr' => array()));

//        echo $gene_config->getConnexionName();die;
        if($gene_config->getConnexionName()) {
            $tables = array();
            foreach($this->getRegister()->getBilan() as $table => $value) {
                $tables[$table] = $table;
            }

            $fb->add('controller_namespace', 'text', array('label' => 'Namespace Contrôleur', 'attr' => array('help' => 'Bo\AdminBundle')));
            $fb->add('controller_name', 'text', array('label' => 'Nom du contrôleur', 'attr' => array('help' => 'Default')));
            $fb->add('table', 'choice', array('choices' => $tables, 'label' => 'Table'));
            $fb->add('action', 'text', array('label' => 'Nom de l\'action', 'attr' => array('help' => 'indexAction')));
            $fb->add('suffix_route', 'text', array('label' => 'Suffixe du nom des routes', 'attr' => array('help' => 'admin.')));
        }
        $fb->add('Enregistrer', 'submit', array('attr' => array('class' => 'btn btn-success')));
        $fb->setRequired(false);
        $form = $fb->getForm();

        $form->handleRequest($request);


        if($request->isMethod('post') || !$session->has('gene.config')) {
            $session->getFlashBag()->add('success', 'Les paramètres ont été enregistrés');

            $data = $form->getData();

            $c = $this->getDoctrine()->getConnection($data['connexion_name']);

            $r = array(
                'connexion_name' => $data['connexion_name'],
                'database_name' => $c->getDatabase(),
                'database_host' => $c->getHost(),
                'database_user' => $c->getUsername(),
                'database_password' => $c->getPassword(),
                'generator' => $generator,
            );

            foreach($data as $key => $value) {
                $r[$key] = $value;
            }

            $session->set('gene.config', $r);

            foreach($data as $key => $value) {
                $session->set($key, $value);
            }

            return $this->redirect($this->generateUrl($request->get('_route')));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/table_change", name="gene.table_change")
     */
    public function table_changeAction(Request $request)
    {
        $session = $request->getSession();

        $session->set('table', $request->get('table'));

        return $this->redirect($this->generateUrl($request->get('_route')));
//        return new JsonResponse(array());
    }

}

//        fputs($filo, 'Texte à écrire');
//        $finder->sortByName();
//        foreach($finder as $file) {
//            echo '<div>' . $file;
////            echo '<div>' . $contents = $file->getContents() . '</div>';
//        }
//        die;
//        $filo = fopen($form_bundle_dir . '/test.php', 'a+');


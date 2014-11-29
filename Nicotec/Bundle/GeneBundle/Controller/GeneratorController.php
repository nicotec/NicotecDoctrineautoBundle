<?php

namespace WsGene\EditBundle\Controller;

use Doctrine\Common\Cache\ApcCache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WsGene\EditBundle\ClassVendor\GeshiColor;
use WsGene\EditBundle\Controller\ReController as Controller;

class GeneratorController extends Controller {

    /**
     * Générer doctrine2 (sur la base par default uniquement pour l'instant)
     * @Route("/doctrine2", name="gene.doctrine2")
     * @Template()
     */
    public function doctrine2Action(Request $request)
    {
        $apc = new ApcCache();
        $apc->flushAll();

        $eg = $this->getEntityGenerator();

        if($request->get('put')) {

            $eg->setIsFileContent(true);
            $eg->dispatch(true);

            return $this->redirect($this->generateUrl('gene.doctrine2'));
        }
        else {
            $eg->dispatch(true);
        }

        return array(
            'eq' => $eg,
        );
    }

    /**
     * @Route("/form_controller", name="gene.form_controller")
     * @Template()
     */
    public function form_controllerAction()
    {
        $codes = array(
            'form_simple' => $this->getGenerator('Form\FileFormSimpleController')->execute(),
            'form_crud' => $this->getGenerator('Form\FileFormCrudController')->execute(),
            'form_factorise_a' => $this->getGenerator('Form\FileFormFactoriseAController')->execute(),
            'form_factorise_b' => $this->getGenerator('Form\FileFormFactoriseBController')->execute(),
        );

        return array(
            'cc' => new GeshiColor($this->get('kernel')),
            'codes' => $codes,
        );
    }

    /**
     * @Route("/form_th", name="gene.form_th")
     * @Template()
     */
    public function form_thAction()
    {
        $codes = array(
            'form_type' => $this->getGenerator('Form\FileFormType')->execute(),
            'form_handler' => $this->getGenerator('Form\FileFormHandler')->execute(),
        );

        return array(
            'cc' => new GeshiColor($this->get('kernel')),
            'codes' => $codes,
        );
    }

//    public function copieFileFormEditAction()
//    {
//        $request = $this->getRequest();
//
//        $pathfile = $this->get('kernel')->getRootDir() . '/../' . $request->get('file');
//        $function = $request->get('function');
//
//
//        $g = $this->getGenerator($request->get('path_class'));
//        $codes = $g->{$function}();
//
//        $spl = new SplFileObject($pathfile, 'w');
//        $spl->fwrite($codes['code']);
//
//        return new JsonResponse(array());
//    }

    /**
     * @Route("/form_twig_crud", name="gene.form_twig_crud")
     * @Template()
     */
    public function form_twig_crudAction()
    {
        $codes = array(
            'form_twig_new_edit' => $this->getGenerator('Form\FileFormTwigNewEdit')->execute(),
            'form_twig_new' => $this->getGenerator('Form\FileFormTwigNew')->execute(),
            'form_twig_edit' => $this->getGenerator('Form\FileFormTwigEdit')->execute(),
            'form_twig_new_edit_dev' => $this->getGenerator('Form\FileFormTwigNewEditDev')->execute(),
            'form_twig_new_dev' => $this->getGenerator('Form\FileFormTwigNewDev')->execute(),
            'form_twig_edit_dev' => $this->getGenerator('Form\FileFormTwigEditDev')->execute(),
            'form_twig_show_all' => $this->getGenerator('Form\FileFormTwigShowAll')->execute(),
//            'form_edit' => $this->getGenerator('Form\FormEdit')->execute(),
//            'form_show' => $this->getGenerator('Form\FormShow')->execute(),
        );

        return array(
            'cc' => new GeshiColor($this->get('kernel')),
            'codes' => $codes,
        );
    }

    /**
     * @Route("/form_autre", name="gene.form_autre")
     * @Template()
     */
    public function form_autreAction()
    {
        $codes = array(
            'form_file_gerer' => $this->getGenerator('Form\FileFormFileGerer')->execute(),
//            'form_edit' => $this->getGenerator('Form\FormEdit')->execute(),
//            'form_show' => $this->getGenerator('Form\FormShow')->execute(),
        );

        return array(
            'cc' => new GeshiColor($this->get('kernel')),
            'codes' => $codes,
        );
    }

    /**
     * @Route("/form_factorise", name="gene.form_factorise")
     * @Template()
     */
    public function form_factoriseAction()
    {
        $g = $this->getGenerator('Form\FormGenerator');
        $codes = array(
            'form_action_factorise' => $g->getFormActionFactorise(),
            'form_action_factorise_ui' => $g->getFormActionUi(),
            'form_type' => $g->getFormType(),
            'form_handler' => $g->getFormHandler(),
            'form_file' => $g->getFormFile(),
        );

        return array(
            'cc' => new GeshiColor($this->get('kernel')),
            'bilan' => $g->getBilan(),
            'codes' => $codes,
        );
    }

    /**
     * @Route("/form_twig", name="gene.form_twig")
     * @Template()
     */
    public function form_twigAction()
    {
        $g = $this->getGenerator('Form\TwigGenerator');
        $codes = array(
            'twig_form' => $g->getTwigForm(),
            'twig_form_developpe' => $g->getTwigFormDeveloppe(),
            'twig_form_theme' => $g->getTwigFormTheme(),
        );

        return array(
            'cc' => new GeshiColor($this->get('kernel')),
            'bilan' => $g->getBilan(),
            'codes' => $codes,
        );
    }

    /**
     * @Route("/query", name="gene.query")
     * @Template()
     */
    public function queryAction()
    {
        $g = $this->getGenerator('Doctrine\QueryGenerator');

        $codes = array(
            'select' => $g->getDoctrineSelect(),
            'update' => $g->getDoctrineUpdate(),
            'is_null' => $g->getDoctrineIsNull(),
            'delete' => $g->getDoctrineDeleteOne(),
            'delete_all' => $g->getDoctrineDeleteAll(),
        );

        return array(
            'cc' => new GeshiColor($this->get('kernel')),
            'bilan' => $g->getBilan(),
            'codes' => $codes,
        );
    }

    /**
     * @Route("/pager", name="gene.pager")
     * @Template()
     */
    public function pagerAction()
    {
        $g = $this->getGenerator('Doctrine\PagerGenerator');

        $codes = array(
            'pager_action' => $g->getPagerAction(),
            'pager_twig' => $g->getPagerTwig(),
        );

        return array(
            'cc' => new GeshiColor($this->get('kernel')),
            'bilan' => $g->getBilan(),
            'codes' => $codes,
        );
    }

    /**
     * @Route("/sortable", name="gene.sortable")
     * @Template()
     */
    public function sortableAction(Request $request)
    {
        $this->getRegister();
        $codes = array();

        return array(
            'cc' => new GeshiColor($this->get('kernel')),
            'bilan' => $request->get('bilan'),
            'codes' => $codes,
        );
    }

//    public function createFileAction()
//    {
//        $request = $this->getRequest();
//        $request->request->add(array(
//            'namespace_bundle_controller' => 'Bo\EcoleBundle',
//            'controller' => 'AdminEcole',
////            'action' => '',
////            'table' => 'don',
//            'suffix_route' => 'admin.',
//        ));
//        if(!$request->get('namespace_bundle_entity')) {
//            $gene_config = $this->getGeneConfig();
//            $request->request->set('namespace_bundle_entity', $gene_config->getNamespaceBundle());
//        }
//        else {
//            die('coming soon');
//        }
//
//
//        $fs = new Filesystem();
//        $finder = new Finder();
//        $finder->files()->in($this->get('kernel')->getRootDir() . '/../src/' . str_replace('\\', '/', $request->get('namespace_bundle_controller')) . '/Form');
//
//        $form_bundle_dir = $this->get('kernel')->getRootDir() . '/../src/' . str_replace('\\', '/', $request->get('namespace_bundle_controller')) . '/Form';
//        $fs->mkdir($form_bundle_dir, 0777);
//    }

    /**
     * @Route("/copie", name="gene.copie")
     */
    public function copieAction()
    {
        die('xcvxcv');

        $pathfile = $this->get('kernel')->getRootDir() . '/../' . $request->get('file');
        $function = $request->get('function');


        $g = $this->getGenerator($request->get('path_class'));

        $codes = $g->{$function}();

//        echo $pathfile;
//        echo $codes['code'];
//        die;


        $spl = new SplFileObject($pathfile, 'w');
        if($spl->isReadable()) {
            $fs = new Filesystem();
            $fs->copy($pathfile, $this->get('kernel')->getRootDir() . '/historique/' . date('YmdHis'));
        }
        die('sdfsdf');
        $spl->fwrite($codes['code']);
//echo 'eee';
//        $root_dir;

        return new JsonResponse(array());
    }

}

//        fputs($filo, 'Texte ? ?crire');
//        $finder->sortByName();
//        foreach($finder as $file) {
//            echo '<div>' . $file;
////            echo '<div>' . $contents = $file->getContents() . '</div>';
//        }
//        die;
//        $filo = fopen($form_bundle_dir . '/test.php', 'a+');


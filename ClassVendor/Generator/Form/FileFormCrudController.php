<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;

use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormCrudController extends FormGeneratorExtends {

    public function execute()
    {
        //namespace
        $this->setNamespace("{$this->getNamespaceBundleController()}\Controller");

        //les uses ini
        $this->setUse("{$this->getNamespaceBundleEntity()}\Entity\\{$this->getTableCamelize()}");
        $this->setUse("Sensio\Bundle\FrameworkExtraBundle\Configuration\Route");
        $this->setUse("Sensio\Bundle\FrameworkExtraBundle\Configuration\Template");
        $this->setUse("Symfony\Bundle\FrameworkBundle\Controller\Controller");

        //les actions ini
        $code0 = $this->getShowAllAction();
        $code1 = $this->getNewAction();
        $code2 = $this->getEditAction();
        $code3 = $this->getDeleteAction();

        //assemblage
        $codeA = $this->getFileClassStart();
        $codeB = $this->getFileClassName();
        $codeZ = $this->getFileClassEnd();


        $code = $codeA . $codeB . $code0 . $code1 . $code2 . $code3 . $codeZ;


        return array(
            'titre' => 'Simple (2a)',
            'help' => 'new + edit simple en 2 actions dans le contrôleur',
            'file' => str_replace('\\', '/', 'src/' . $this->getNamespaceBundleController() . '/Controller/' . $this->camelize($this->getTable()) . 'Controller.php'),
            'code' => $code,
            'path_class' => $this->getPathClass(),
            'function' => __FUNCTION__,
        );
    }

    public function getFileClassName()
    {
        $code = <<<eof
/**
 * file auto: FileFormCrudController
 * @Route("")
 */
class {$this->getTableCamelize()}Controller extends Controller {


eof;

        return $code;
    }

    protected function getNewAction()
    {
        $code = <<<eof
    /**
     * @Route("/{$this->getTable()}_new", name="{$this->getSuffixRoute()}{$this->getTable()}_new")
     * @Template()
     */
    public function {$this->getTable()}_newAction()
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->getRequest();
        \$session = \$request->getSession();

        \${$this->getTable()} = new {$this->getTableCamelize()}();

        \$form = \$this->createFormBuilder(\${$this->getTable()})

eof;


        if($this->getGene()->hasChampsFiles($this->getTable())) {
            $code .= <<<eof
        \${$this->getTable()}->uploadFiles(\$this->get('kernel')->getRootDir() . '/../web/uploads');


eof;
        }


        $code .= $this->regAdd();
        $code .= <<<eof
            ->getForm()
        ;

        \$form->handleRequest(\$request);

        if(\$request->isMethod('post')) {

            if(\$form->isValid()){
                \$session->getFlashBag()->set('success', 'Enregistrement ajouté avec succès');

                \$em->persist(\${$this->getTable()});
                \$em->flush();

                return \$this->redirect(\$this->generateUrl('{$this->getSuffixRoute()}{$this->getTable()}_edit', array('id' => \${$this->getTable()}->getId())));
           }
        }

        return array(
            '{$this->getTable()}' => \${$this->getTable()},
            'form' => \$form->createView(),
        );
    }


eof;

        return $code;
    }

    protected function getEditAction()
    {
        $code = <<<eof
   /**
     * @Route("/{$this->getTable()}_edit/{id}", name="{$this->getSuffixRoute()}{$this->getTable()}_edit", requirements={"id"="\d+"})))
     * @Template()
     */
    public function {$this->getTable()}_editAction(\$id)
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->getRequest();
        \$session = \$request->getSession();

        \${$this->getTable()} = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->find(\$id);

        \$form = \$this->createFormBuilder(\${$this->getTable()})

eof;

        $code .= $this->regAdd();
        $code .= <<<eof
            ->getForm()
        ;

        \$form->handleRequest(\$request);

        if(\$request->isMethod('post')) {

            if(\$form->isValid()){
                \$session->getFlashBag()->set('success', 'Enregistrement mis à jour avec succès');

                \$em->persist(\${$this->getTable()});
                \$em->flush();

                return \$this->redirect(\$this->generateUrl('{$this->getSuffixRoute()}{$this->getTable()}_edit', array('id' => \${$this->getTable()}->getId())));
           }
        }

        return array(
            '{$this->getTable()}' => \${$this->getTable()},
            'form' => \$form->createView(),
        );
    }


eof;

        return $code;
    }

    protected function getShowAllAction()
    {
        $form_delete = new FormShowAll($this->request, $this->kernel);
        return $form_delete->getAction();
    }

    protected function getDeleteAction()
    {
        $form_delete = new FormDelete($this->request, $this->kernel);
        return $form_delete->getAction();
    }

}

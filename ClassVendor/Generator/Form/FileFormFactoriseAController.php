<?php

namespace WsGene\EditBundle\ClassVendor\Generator\Form;

use WsGene\EditBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormFactoriseAController extends FormGeneratorExtends {

    public function execute()
    {
        //namespace
        $this->setNamespace("{$this->getNamespaceBundleController()}\Controller");

        //les uses ini
        $this->setUse("{$this->getNamespaceBundleEntity()}\Entity\\{$this->getTableCamelize()}");
        $this->setUse("{$this->getNamespaceBundleController()}\Form\\{$this->getTableCamelize()}Type");
        $this->setUse("{$this->getNamespaceBundleController()}\Form\\{$this->getTableCamelize()}Handler");
        $this->setUse("Sensio\Bundle\FrameworkExtraBundle\Configuration\Route");
        $this->setUse("Sensio\Bundle\FrameworkExtraBundle\Configuration\Template");
        $this->setUse("Symfony\Bundle\FrameworkBundle\Controller\Controller");

        //les actions ini
        $code0 = $this->getShowAllAction();
        $code1 = $this->getNewAndEditAction();
        $code3 = $this->getDeleteAction();

        //assemblage
        $codeA = $this->getFileClassStart();
        $codeB = $this->getFileClassName();
        $codeZ = $this->getFileClassEnd();

        $code = $codeA . $codeB . $code0 . $code1 . $code3 . $codeZ;

        return array(
            'titre' => 'Factorise (1a)',
            'help' => 'new + edit factorisés en 1 actions dans le contrôleur',
            'file' => str_replace('\\', '/', 'src/' . $this->getNamespaceBundleController() . '/Controller/' . $this->getTableCamelize() . 'Controller.php'),
            'code' => $code,
            'path_class' => $this->getPathClass(),
            'function' => __FUNCTION__,
        );
    }

    public function getFileClassName()
    {
        $code = <<<eof
/**
 * file auto: FileFormFactoriseAController
 * @Route("")
 */
class {$this->getTableCamelize()}Controller extends Controller {


eof;

        return $code;
    }

    public function getFileEnd()
    {
        $code = <<<eof
}


eof;

        return $code;
    }

    /**
     * new + edit factorise
     * @return type
     */
    protected function getNewAndEditAction()
    {
        $user_service = false;
        if($this->getTable() == 'user') {
            $user_service = ', $this->get(\'security.encoder_factory\')';
        }

        $code = <<<eof
    /**
     * @Route("/{$this->getTable()}_new", name="{$this->getSuffixRoute()}{$this->getTable()}_new")
     * @Route("/{$this->getTable()}_edit", name="{$this->getSuffixRoute()}{$this->getTable()}_edit"))
     * @Template()
     */
    public function {$this->getTable()}_editAction()
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->getRequest();
        \$session = \$request->getSession();

        if(\$request->get('id')) {
            \${$this->getTable()} = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->find(\$request->get('id'));
            \$message = 'Enregistrement mis à jour avec succès';
        }
        else {
            \${$this->getTable()} = new {$this->getTableCamelize()}();
            \$message = 'Enregistrement ajouté avec succès';
        }

        \$form = \$this->createForm(new {$this->getTableCamelize()}Type(), \${$this->getTable()});
        \$formHandler = new {$this->getTableCamelize()}Handler(\$form, \$request, \$em{$user_service});

        if(\$formHandler->process()) {
            \$session->getFlashBag()->set('success', \$message);

            return \$this->redirect(\$this->generateUrl('{$this->getSuffixRoute()}{$this->getTable()}_edit', array('id' => \${$this->getTable()}->getId())));
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

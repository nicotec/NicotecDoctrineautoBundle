<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;

use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormSimpleController extends FormGeneratorExtends {

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
        $code1 = $this->getNewEditAction();
        $code2 = $this->getDeleteAction();

        //assemblage
        $codeA = $this->getFileClassStart();
        $codeB = $this->getFileClassName();
        $codeZ = $this->getFileClassEnd();


        $code = $codeA . $codeB . $code0 . $code1 . $code2 . $codeZ;

        return array(
            'titre' => 'Simple (1a)',
            'help' => 'new + edit simple dans le contrôleur',
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
 * file auto: FileFormSimpleController
 * @Route("")
 */
class {$this->getTableCamelize()}Controller extends Controller {


eof;

        return $code;
    }

    public function getNewEditAction()
    {
        $code = <<<eof
    /**
     * @Route("/{$this->getTable()}_new", name="{$this->getSuffixRoute()}{$this->getTable()}_new")
     * @Route("/{$this->getTable()}_edit/{id}", name="{$this->getSuffixRoute()}{$this->getTable()}_edit", requirements={"id"="\d+"})
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

        \$form = \$this->createFormBuilder(\${$this->getTable()})

eof;


        $code .= $this->regAdd();
        $code .= <<<eof
            ->getForm()
        ;

        \$form->handleRequest(\$request);

        if(\$request->isMethod('post')) {

            if(\$form->isValid()){
                \$session->getFlashBag()->set('success', \$message);

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

    public function getDeleteAction()
    {
        $form_delete = new FormDelete($this->request, $this->kernel);
        return $form_delete->getAction();
    }

}

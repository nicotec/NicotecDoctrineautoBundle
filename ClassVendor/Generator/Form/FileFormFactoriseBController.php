<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;

use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormFactoriseBController extends FormGeneratorExtends {

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
        $code1 = $this->getNewAction();
        $code2 = $this->getEditAction();
        $code3 = $this->getDeleteAction();

        //assemblage
        $codeA = $this->getFileClassStart();
        $codeB = $this->getFileClassName();
        $codeZ = $this->getFileClassEnd();

        $code = $codeA . $codeB . $code0 . $code1 . $code2 . $code3 . $codeZ;

        return array(
            'titre' => 'Factorise (2a)',
            'help' => 'new + edit factorisés en 2 actions dans le contrôleur',
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
 * file auto: FileFormFactoriseBController
 * @Route("")
 */
class {$this->getTableCamelize()}Controller extends Controller {


eof;

        return $code;
    }

    protected function getFormJoinAction($class_form_type = false)
    {
        $champs = array();
        if($this->getGene()->hasJoins($this->getTable())) {
            foreach($this->getGene()->getJoins($this->getTable()) as $champ => $array) {
//                var_dump($array);
//                die;
                $champs[] = $champ;
            }
        }

        if(count($champs) == 0) {
            return array(
                'help' => 'Insert + update avec jointure en 2 actions dans le contrôleur<br>' . $this->getNamespaceBundleController() . '\Controller\\' . $this->getTableCamelize(),
                'code' => 'Pas de jointure',
            );
        }

//        $is_champ_file = false;
//        if($this->getGene()->getChampsFiles($this->getTable())) {
//            $is_champ_file = true;
//        }

        $code = <<<eof
<?php

namespace {$this->getNamespaceBundleController()}\Controller;

use {$this->getNamespaceBundleEntity()}\Entity\\{$this->getTableCamelize()};
use {$this->getNamespaceBundleEntity()}\Entity\\{$this->camelize($champs[0])};
use {$this->getNamespaceBundleController()}\Form\\{$this->getTableCamelize()}Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("")
 */
class {$this->getTableCamelize()}Controller extends Controller {

    /**
     * @Route("/{$this->getTable()}_insert/{{$champs[0]}_id}", name="{$this->getSuffixRoute()}{$this->getTable()}_insert", requirements={"{$champs[0]}_id"="\d+"}))))
     * @Template()
     */
    public function {$this->getTable()}_insertAction(\${$champs[0]}_id)
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->getRequest();
        \$session = \$request->getSession();

        \${$champs[0]} = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->camelize($champs[0])}')->find(\${$champs[0]}_id);
        \${$this->getTable()} = new {$this->getTableCamelize()}();
        \${$this->getTable()}->set{$this->camelize($champs[0])}(\${$champs[0]});


eof;

        if($class_form_type === false) {
            $code .= <<<eof
        \$form = \$this->createFormBuilder(\${$this->getTable()})

eof;
            $code .= $this->regAdd();
            $code .= <<<eof
            ->getForm()
        ;
eof;
        }
        else {
            $code .= <<<eof
        \$form = \$this->createForm(new {$this->getTableCamelize()}Type(), \${$this->getTable()});

        \$form->handleRequest(\$request);

        if(\$request->isMethod('post')) {

            if(\$form->isValid()){

eof;
        }
//        if($is_champ_file) {
//            $code .= <<<eof
//                \${$this->getTable()}->uploadFiles(\$this->get('kernel')->getRootDir() . '/../web/uploads');
//
//
//eof;
//        }
        $code .= <<<eof
                \$session->getFlashBag()->set('success', 'Enregistrement insérer avec succès');

                \$em->persist(\${$this->getTable()});
                \$em->flush();

                return \$this->redirect(\$this->generateUrl('{$this->getSuffixRoute()}{$this->getTable()}_update', array('id' => \${$this->getTable()}->getId())));
            }
        }

//        return new Response(json_encode(array(
//                'html' => \$this->renderView('{$this->getAlias_NSBC()}:{$this->getTableCamelize()}:{$this->getTable()}_insert.html.twig', array(
//                        'form' => \$form->createView(),
//                        '{$this->getTable()}' => \${$this->getTable()},
//                    )))));

        return array(
            '{$this->getTable()}' => \${$this->getTable()},
            'form' => \$form->createView(),
        );
    }


    /**
     * @Route("/{$this->getTable()}_update/{id}", name="{$this->getSuffixRoute()}{$this->getTable()}_update", requirements={"id"="\d+"}))))
     * @Template()
     */
    public function {$this->getTable()}_updateAction(\$id)
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->getRequest();
        \$session = \$request->getSession();

        \${$this->getTable()} = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->find(\$id);
        \${$champs[0]} = \${$this->getTable()}->get{$this->camelize($champs[0])}();

eof;

        if($class_form_type === false) {
            $code .= <<<eof
        \$form = \$this->createFormBuilder(\${$this->getTable()})

eof;
            $code .= $this->regAdd();
            $code .= <<<eof
            ->getForm()
        ;
eof;
        }
        else {
            $code .= <<<eof
        \$form = \$this->createForm(new {$this->getTableCamelize()}Type(), \${$this->getTable()});


eof;
        }
        $code .= <<<eof
        if(\${$this->getTable()}) {

            \$form->handleRequest(\$request);

            if(\$request->isMethod('post')) {

                if(\$form->isValid()){

eof;
//        if($is_champ_file) {
//            $code .= <<<eof
//                    \${$this->getTable()}->uploadFiles(\$this->get('kernel')->getRootDir() . '/../web/uploads');
//
//
//eof;
//        }
        $code .= <<<eof
                    \$session->getFlashBag()->set('success', 'Enregistrement mis avec succès');

                    \$em->persist(\${$this->getTable()});
                    \$em->flush();

                    return \$this->redirect(\$this->generateUrl('{$this->getSuffixRoute()}{$this->getTable()}_update', array('id' => \${$this->getTable()}->getId())));
                }
            }
        }
        else {
            throw \$this->createNotFoundException('Pas d\'enregistrement : ' . \$id);
        }

//        return new Response(json_encode(array(
//                'html' => \$this->renderView('{$this->getAlias_NSBC()}:{$this->getTableCamelize()}:{$this->getTable()}_update.html.twig', array(
//                        'form' => \$form->createView(),
//                        '{$this->getTable()}' => \${$this->getTable()},
//                    )))));

        return array(
            '{$this->getTable()}' => \${$this->getTable()},
            'form' => \$form->createView(),
        );
    }


eof;

        return array(
            'help' => 'Insert + update avec jointure en 2 actions dans le contrôleur<br>' . $this->getNamespaceBundleController() . '\Controller\\' . $this->getTableCamelize(),
            'code' => $code,
        );
    }

    /**
     * new + edit factorise
     * @return type
     */
    protected function getFormActionFactorise()
    {
        $user_service = false;
        if($this->getTable() == 'user') {
            $user_service = ', $this->get(\'security.encoder_factory\')';
        }

        $code = <<<eof
    /**
     * @Route("/{$this->getTable()}_edit", name="{$this->getSuffixRoute()}{$this->getTable()}_edit")
     * @Template()
     */
    public function {$this->getTable()}_editAction()
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->getRequest();
        \$session = \$request->getSession();

        if(\$request->get('id')) {
            \${$this->getTable()} = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->find(\$request->get('id'));
            \$message = 'Enregistrement ajouté avec succès';
        }
        else {
            \${$this->getTable()} = new {$this->getTableCamelize()}();
            \$message = 'Enregistrement mis à jour avec succès';
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

        return array(
//            'file' => str_replace('\\', '/', 'src/' . $this->getNamespaceBundleController() . '/Form/' . $this->camelize($this->getTable()) . 'Type.php'),
            'code' => $code,
            'path_class' => $this->getPathClass(),
            'function' => __FUNCTION__,
        );
    }

    protected function getNewAction()
    {
        $user_service = false;
        if($this->getTable() == 'user') {
            $user_service = ', $this->get(\'security.encoder_factory\')';
        }

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

        \$form = \$this->createForm(new {$this->getTableCamelize()}Type(), \${$this->getTable()});
        \$formHandler = new {$this->getTableCamelize()}Handler(\$form, \$request, \$em{$user_service});

        if(\$formHandler->process()) {
            \$session->getFlashBag()->set('success', 'Enregistrement ajouté avec succès');

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

    protected function getEditAction()
    {
        $user_service = false;
        if($this->getTable() == 'user') {
            $user_service = ', $this->get(\'security.encoder_factory\')';
        }

        $code = <<<eof
    /**
     * @Route("/{$this->getTable()}_edit/{id}", name="{$this->getSuffixRoute()}{$this->getTable()}_edit", requirements={"id"="\d+"}))
     * @Template()
     */
    public function {$this->getTable()}_editAction(\$id)
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->getRequest();
        \$session = \$request->getSession();

        \${$this->getTable()} = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->find(\$id);
        if(\${$this->getTable()}) {
            \$form = \$this->createForm(new {$this->getTableCamelize()}Type(), \${$this->getTable()});
            \$formHandler = new {$this->getTableCamelize()}Handler(\$form, \$request, \$em{$user_service});

            if(\$formHandler->process()) {
                \$session->getFlashBag()->set('success', 'Enregistrement mis à jour avec succès');

                return \$this->redirect(\$this->generateUrl('{$this->getSuffixRoute()}{$this->getTable()}_edit', array('id' => \${$this->getTable()}->getId())));
            }
        }
        else {
            return \$this->createNotFoundException('Enregistrement vide');
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

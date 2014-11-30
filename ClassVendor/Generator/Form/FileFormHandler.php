<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormHandler extends FormGeneratorExtends {

    protected $is_table_user;

    public function __construct(Request $request, Kernel $kernel)
    {
        parent::__construct($request, $kernel);

        if($this->getTable() == 'user') {
            $this->is_table_user = true;
        }
    }

    public function execute()
    {
        //namespace
        $this->setNamespace("{$this->getNamespaceBundleController()}\Form");

        //les uses ini
        $this->setUse("{$this->getNamespaceBundleEntity()}\Entity\\{$this->getTableCamelize()}");
        $this->setUse("Doctrine\ORM\EntityManager");
        $this->setUse("Symfony\Component\Form\Form");
        $this->setUse("Symfony\Component\HttpFoundation\Request");
        $this->setUse("Symfony\Component\Validator\Validator");
        if($this->is_table_user) {
            $this->setUse("Symfony\Component\Security\Core\Encoder\EncoderFactory");
        }

        //les actions ini
        $code1 = $this->getHandler();

        //assemblage
        $codeA = $this->getFileClassStart();
        $codeB = $this->getFileClassName();
        $codeZ = $this->getFileClassEnd();

        $code = $codeA . $codeB . $code1 . $codeZ;

        return array(
            'titre' => 'FormHandler',
            'help' => 'Insert + update simple en 2 actions dans le contrôleur',
            'file' => str_replace('\\', '/', 'src/' . $this->getNamespaceBundleController() . '/Form/' . $this->camelize($this->getTable()) . 'Handler.php'),
            'code' => $code,
            'path_class' => $this->getPathClass(),
            'function' => __FUNCTION__,
        );
    }

    public function getFileClassName()
    {
        $code = <<<eof
class {$this->getTableCamelize()}Handler {

{$this->getProtected()}


eof;

        return $code;
    }

    protected function getHandler()
    {
        $encode_factory = false;
        if($this->is_table_user) {
            $this->setProtected('encoder_factory');
            $encode_factory = ', EncoderFactory $encoder_factory';
        }

        $this->setProtected('form');
        $this->setProtected('request');
        $this->setProtected('em');

        $code = <<<eof
    public function __construct(Form \$form, Request \$request, EntityManager \$em{$encode_factory})
    {
        \$this->form = \$form;
        \$this->em = \$em;
        \$this->request = \$request;

eof;
        if($this->is_table_user) {
            $code .= <<<eof
        \$this->encoder_factory = \$encoder_factory;

eof;
        }
        $code .= <<<eof
    }

    public function process()
    {
        if(\$this->request->getMethod() == 'POST') {
            \$this->form->bind(\$this->request);
            \${$this->getTable()} = \$this->form->getData();

eof;
        if($this->is_table_user) {
            $code .= <<<eof
            \$this->repassword = \$this->request->get('repassword');

eof;
        }
        $code .= <<<eof

            //\$form_address_livraison = \$this->form['addresss'][0];
            //\$form_address_livraison->get('zip_code')->addError(new FormError('Le code postal livraison est invalide'));


            if(\$this->form->isValid()) {
                return \$this->onSuccess(\${$this->getTable()});
            }
        }

        return false;
    }

    public function onSuccess({$this->getTableCamelize()} \${$this->getTable()})
    {

eof;
        if($this->is_table_user) {
            $code .= <<<eof
        \$encoder = \$this->encoder_factory->getEncoder(\$user);
        \$password = \$encoder->encodePassword(\$user->getPassword(), \$user->getSalt());
        \$user->setPassword(\$password);


eof;
        }

        $code .= <<<eof
        //\${$this->getTable()}->preUpload();
        \$this->em->persist(\${$this->getTable()});
        \$this->em->flush();
        //\${$this->getTable()}->uploadFiles();

        return true;
    }


eof;

        return $code;
    }

//    protected function getFormFile()
//    {
//        $suffix = 'Upload';
//
//        if($this->getGene()->hasChampsFiles($this->getTable())) {
//            foreach($this->getGene()->getChampsFiles($this->getTable()) as $champ => $options) {
//                $code = false;
//                $code .= <<<eof
//    public \${$this->champPearMin($champ)}{$suffix};
//
//eof;
//            }
//            $code .= <<<eof
//
//    public function uploadFiles(\$root_dir_upload)
//    {
//
//eof;
//            foreach($this->getGene()->getChampsFiles($this->getTable()) as $champ => $options) {
//                $code .= <<<eof
//        if(null !== \$this->{$this->champPearMin($champ)}{$suffix}) {
//            \$name_file = '{$champ}_' . sha1(uniqid(mt_rand(), true)) . '.' . \$this->{$this->champPearMin($champ)}{$suffix}->guessExtension();
//
//            \$this->{$this->champPearMin($champ)}{$suffix}->move(\$root_dir_upload, \$name_file);
//            \$this->set{$this->camelize($this->champPearMin($champ))}(\$name_file);
//
//            unset(\$this->{$champ}{$suffix});
//        }
//
//eof;
//            }
//            $code .= <<<eof
//    }
//
//eof;
//        }
//        else {
//            $code = 'Pas de champ de fichier dans cette table';
//        }
//
//
//        return array(
//            'help' => '',
//            'fichier' => $this->getNamespaceBundleEntity() . '\Model\\' . $this->camelize($this->getTable()) . 'Base.php',
//            'code' => $code,
//        );
//    }
}

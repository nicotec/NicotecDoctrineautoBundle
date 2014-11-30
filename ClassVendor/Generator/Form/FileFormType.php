<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;

use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormType extends FormGeneratorExtends {

    public function execute()
    {
        //namespace
        $this->setNamespace("{$this->getNamespaceBundleController()}\Form");

        //les uses ini
        $this->setUse("Symfony\Component\Form\AbstractType");
        $this->setUse("Symfony\Component\Form\FormBuilderInterface");
        $this->setUse("Symfony\Component\OptionsResolver\OptionsResolverInterface");

        //les actions ini
        $code1 = $this->getType();

        //assemblage
        $codeA = $this->getFileClassStart();
        $codeB = $this->getFileClassName();
        $codeZ = $this->getFileClassEnd();

        $code = $codeA . $codeB . $code1 . $codeZ;

        return array(
            'titre' => 'FormType',
            'help' => 'Insert + update simple en 2 actions dans le contrôleur',
            'file' => str_replace('\\', '/', 'src/' . $this->getNamespaceBundleController() . '/Form/' . $this->camelize($this->getTable()) . 'Type.php'),
            'code' => $code,
            'path_class' => $this->getPathClass(),
            'function' => __FUNCTION__,
        );
    }

    public function getFileClassName()
    {
        $code = <<<eof
class {$this->getTableCamelize()}Type extends AbstractType {


eof;

        return $code;
    }

    protected function getType()
    {
        $code = <<<eof
    public function buildForm(FormBuilderInterface \$builder, array \$options)
    {
        \$builder

eof;
        $code .= $this->regAdd(true);
        $code .= <<<eof
    }

    public function setDefaultOptions(OptionsResolverInterface \$resolver)
    {
        \$resolver->setDefaults(array(
                'data_class' => '{$this->getNamespaceBundleEntity()}\Entity\\{$this->getTableCamelize()}'
        ));
    }

    public function getName()
    {
        return '{$this->getTable()}_type';
    }


eof;

        return $code;
    }

}

<?php

namespace WsGene\EditBundle\ClassVendor\Generator\Form;

use Symfony\Component\HttpFoundation\Request;
use WsGene\EditBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FormShowAll extends FormGeneratorExtends {

    public function getAction()
    {
        $code = <<<eof
    /**
     * @Route("/{$this->getTable()}s", name="{$this->getSuffixRoute()}{$this->getTable()}s")
     * @Template()
     */
    public function {$this->getTable()}sAction()
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->getRequest();
        \$session = \$request->getSession();

        \${$this->getTable()}s = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->findAll();


        return array('{$this->getTable()}s' => \${$this->getTable()}s);
    }


eof;

        return $code;
    }

}

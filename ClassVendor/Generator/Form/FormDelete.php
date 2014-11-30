<?php

namespace WsGene\EditBundle\ClassVendor\Generator\Form;

use WsGene\EditBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FormDelete extends FormGeneratorExtends {

    public function getAction()
    {
        $code = <<<eof
    /**
     * @Route("/{$this->getTable()}_delete/{id}", name="{$this->getSuffixRoute()}{$this->getTable()}_delete", requirements={"id"="\d+"})
     * @Template()
     */
    public function {$this->getTable()}_deleteAction(\$id)
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->getRequest();
        \$session = \$request->getSession();

        \${$this->getTable()} = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->find(\$id);

        \$em->remove(\${$this->getTable()});
        \$em->flush();

        \$session->getFlashBag()->set('success', 'Enregistrement supprimé avec succès');

      return \$this->redirect(\$this->generateUrl('{$this->getSuffixRoute()}{$this->getTable()}s'));
    }


eof;

        return $code;
    }

}

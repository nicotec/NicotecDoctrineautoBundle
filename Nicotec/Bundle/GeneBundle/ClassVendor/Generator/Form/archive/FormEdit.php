<?php

namespace WsGene\EditBundle\ClassVendor\Generator\Form;

use Symfony\Component\HttpFoundation\Request;
use WsGene\EditBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FormSelect extends FormGeneratorExtends {

    protected $request;
    protected $kernel;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getShowAction()
    {
        $code = <<<eof
    /**
     * @Route("/{$this->getTable()}_show", name="{$this->getSuffixRoute()}{$this->getTable()}_show")
     * @Template()
     */
    public function {$this->getTable()}_showAction(\$id)
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->getRequest();
        \$session = \$request->getSession();

        \${$this->getTable()} = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->findAll(\$id);

        \$em->remove(\${$this->getTable()});
        \$em->flush();


      return array();

        \$this->redirect(\$this->generateUrl('{$this->getSuffixRoute()}{$this->getTable()}s'));


        \$request = \$this->getRequest();
        \$session = \$request->getSession();
        \$em = \$this->getDoctrine()->getManager();
        \$etat = \$request->get('etat');

        if(!is_numeric(\$request->get('page'))) {
            \$page = 1;
        }
        else {
            \$page = \$request->get('page');
        }

        if(!is_numeric(\$request->get('link')) || \$request->get('link') > 50) {
            \$link = 10;
        }
        else {
            \$link = \$request->get('link');
        }

        \$articles = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->createQueryBuilder('a');



   }







eof;

        return $code;
    }

}


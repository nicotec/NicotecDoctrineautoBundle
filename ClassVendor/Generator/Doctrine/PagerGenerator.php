<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Doctrine;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class PagerGenerator extends FormGeneratorExtends {

    protected $request;
    protected $kernel;
    protected $bilan;

    public function __construct(Request $request, Kernel $kernel)
    {
        $this->request = $request;
        $this->kernel = $kernel;
        $this->bilan = $request->get('bilan');
    }


    public function getPagerAction()
    {
        $code = <<<eof
    /**
     * @Route("/{$this->getTable()}s/{page}", name="{$this->getSuffixRoute()}{$this->getTable()}s", defaults={"page" = 1})
     * @Template()
     */
    public function {$this->getTable()}sAction()
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->get('request');

        \$query_{$this->getTable()}s = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->createQueryBuilder('a')
//            ->where('a.')
//            ->andWhere('a.')
//            ->orderBy('')
//            ->addOrderBy('')
        ;

        \$adapter = new DoctrineORMAdapter(\$query_{$this->getTable()}s);
        \$pagerfanta = new Pagerfanta(\$adapter);
        \$pagerfanta->setMaxPerPage(20);
        \$page = \$request->get('page', 1);
        try {
            \$pagerfanta->setCurrentPage(\$page);
        }
        catch(NotValidCurrentPageException \$e) {
            throw \$this->createNotFoundException('numéro de page introuvable');
        }

        \$view = new PagerView(\$this->get('router'), '{$this->getSuffixRoute()}{$this->getTable()}s');
        \$pager_html = \$view->render(\$pagerfanta, array(
            'proximity' => '3',
        ));

        return \$this->render('{$this->getAlias_NSBC()}:{$this->getTableCamelize()}:{$this->getTable()}s.html.twig', array(
                    'pager' => \$pagerfanta,
                    'pager_html' => \$pager_html,
                ));
    }

eof;

        return array(
            'help' => $this->getNamespaceBundleController() . '\Form\\' . $this->camelize($this->getTable()) . 'Handler.php',
            'code' => $code,
        );
    }

    public function getPagerTwig()
    {
        $code = <<<eof
{% extends '{$this->getAlias_NSBC()}::layout.html.twig' %}

    {{ pager_html|raw }}

    <br>

    <table class="">
        <tr>

eof;
        $code .= $this->regTableTh();
        $code .= <<<eof
        </tr>
        {% for {$this->getTable()} in pager.getCurrentPageResults %}
        <tr>

eof;
        $code .= $this->regTableTd();
        $code .= <<<eof
        </tr>
        {% endfor %}
    </table>

    <br>

    {{ pager_html|raw }}

eof;

        return array(
            'help' => $this->getNamespaceBundleController() . '\Form\\' . $this->camelize($this->getTable()) . 'Handler.php',
            'code' => $code,
        );
    }


}

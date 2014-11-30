<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Doctrine;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class Query extends FormGeneratorExtends {

    protected $request;
    protected $kernel;
    protected $gene;
    protected $bilan;
    protected $log;

    public function __construct(Request $request, Kernel $kernel)
    {
        $this->request = $request;
        $this->kernel = $kernel;
        $this->gene = $request->get('gene');
        $this->bilan = $request->get('bilan');

        $log = new Logger('FormGenerator');
        $log->pushHandler(new StreamHandler($kernel->getRootDir() . '/logs/DoctrineGenerator.log'));
        $this->log = $log;
    }

    public function getDoctrineSelect()
    {
        $code = <<<eof
   \$this->createQueryBuilder('a')
       ->where('a.champ = :champ')
       ->setParameter('champ', 'value')
       ->getQuery()
       ->getResult()
eof;


        return array(
            'fichier' => $this->getNamespaceBundleController() . '\Form\\' . $this->camelize($this->getTable()) . 'Handler.php',
            'code' => $code,
        );
    }

    public function getDoctrineUpdate()
    {
        $code = <<<eof
    \$em = \$this->getDoctrine()->getManager();

    \${$this->getTable()} = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->find(\$id);
{$this->regDoctrineSet()}

    \$em->flush();

eof;

        return array(
            'fichier' => $this->getNamespaceBundleController() . '\Form\\' . $this->camelize($this->getTable()) . 'Handler.php',
            'code' => $code,
        );
    }

    public function getDoctrineDeleteOne()
    {
        $code = <<<eof
    \$em = \$this->getDoctrine()->getManager();

    \${$this->getTable()} = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->find(\$id);

    \$em->remove(\${$this->getTable()});
    \$em->flush();

eof;

        return array(
            'fichier' => $this->getNamespaceBundleController() . '\Form\\' . $this->camelize($this->getTable()) . 'Handler.php',
            'code' => $code,
        );
    }

    public function getDoctrineDeleteAll()
    {
        $code = <<<eof
    \$em = \$this->getDoctrine()->getManager();

    \${$this->getTable()}s = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->findAll();

    foreach(\${$this->getTable()}s as \${$this->getTable()}){
        \$em->remove(\${$this->getTable()});
    }
    \$em->flush();

eof;

        return array(
            'fichier' => $this->getNamespaceBundleController() . '\Form\\' . $this->camelize($this->getTable()) . 'Handler.php',
            'code' => $code,
        );
    }

    public function getPagination()
    {
        $code = <<<eof
    /**
     * @Route("/{$this->getTable()}s", name="{$this->getTable()}s")
     * @Template()
     */
    public function {$this->getTable()}Action(\$id)
    {
        \$em = \$this->getDoctrine()->getManager();
        \$request = \$this->get('request');

        \$query_{$this->getTable()}s = \$em->getRepository('{$this->getAlias_NSBE()}:{$this->getTableCamelize()}')->createQueryBuilder('a')
            ->where('a.')
            ->andWhere('a.')
            ->orderBy('')
            ->addOrderBy('')
        ;

        \$adapter = new DoctrineORMAdapter(\$query_{$this->getTable()}s);
        \$pagerfanta = new Pagerfanta(\$adapter);
        \$pagerfanta->setMaxPerPage(20);
        \$page = \$request->query->get('page', 1);
        try {
            \$pagerfanta->setCurrentPage(\$page);
        }
        catch(\Pagerfanta\Exception\NotValidCurrentPageException \$e) {
            \$this->createNotFoundException();
        }

        \$routeGenerator = function(\$page) {
                    return '?page=' . \$page;
                };
        \$view = new PaginationView();
        \$pager_html = \$view->render(\$pagerfanta, \$routeGenerator, array(
            'proximity' => '3',
                ));

        return \$this->render('{$this->getAlias_NSBC()}:Action:{$this->getTable()}s.html.twig', array(
                    'pager' => \$pagerfanta,
                    'pager_html' => \$pager_html,
                ));
    }

eof;

        return array(
            'fichier' => $this->getNamespaceBundleController() . '\Form\\' . $this->camelize($this->getTable()) . 'Handler.php',
            'code' => $code,
        );
    }

    public function getPaginationTwig()
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
            'fichier' => $this->getNamespaceBundleController() . '\Form\\' . $this->camelize($this->getTable()) . 'Handler.php',
            'code' => $code,
        );
    }

}

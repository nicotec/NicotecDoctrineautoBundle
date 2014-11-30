<?php

namespace WsGene\EditBundle\ClassVendor\Generator\Doctrine;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use WsGene\EditBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class QueryGenerator extends FormGeneratorExtends {

    protected $request;
    protected $kernel;
    protected $bilan;

    public function __construct(Request $request, Kernel $kernel)
    {
        $this->request = $request;
        $this->kernel = $kernel;
        $this->bilan = $request->get('bilan');
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
            'fichier' => $this->getNamespaceBundleController() . '\Form\\' . $this->camelize($this->getTable()) . '????.php',
            'code' => $code,
        );
    }

    public function getDoctrineIsNull()
    {
        $code = <<<eof
   \$this->createQueryBuilder('a')
       ->where('a.champ = :champ')
       ->andWhere('a.champ2 IS NULL')
       ->setParameter('champ', 'value')
       ->getQuery()
       ->getResult()

eof;

        return array(
            'fichier' => $this->getNamespaceBundleController() . '\Form\\' . $this->camelize($this->getTable()) . '????.php',
            'code' => $code,
        );
    }

}

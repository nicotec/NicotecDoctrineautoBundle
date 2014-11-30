<?php

namespace WsGene\EditBundle\ClassVendor\Generator\Entity;

use Symfony\Component\DependencyInjection\Container;
use WsGene\EditBundle\ClassVendor\Generator\GeneFiltre;

class FormatGenerator {

    protected $uses = array(), $namespace, $protecteds = array();

    protected function camelize($id)
    {
        return Container::camelize($id);
    }

    protected function underscore($id)
    {
        return Container::underscore($id);
    }

    protected function champPearMin($champ)
    {
        return substr($champ, 0, 1) . substr($this->camelize($champ), 1);
    }

    protected function label($champ)
    {
        return str_replace('_', ' ', $champ);
    }

    /**
     *
     * @return GeneFiltre
     */
    public function getGene()
    {
        return $this->gene_filtre;
    }

    public function setUse($use, $as = false)
    {
        $this->uses[$use] = array('use' => $use, 'as' => $as);
    }

    public function getUses()
    {
        $code = false;

        foreach($this->uses as $use) {
            if($use['as']) {
                $code .= 'use ' . $use['use'] . ' as ' . $use['as'] . ';
';
            }
            else {
                $code .= 'use ' . $use['use'] . ';
';
            }
        }

        return $code;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getNamespace()
    {
        return 'namespace ' . $this->namespace . ';
';
    }

    public function setUseRequest($use, $as = false)
    {
        $uses = $this->request->get('uses');
        $uses[$use] = array('use' => $use, 'as' => $as);
        $this->request->set('uses', $uses);
    }

    public function setProtected($nom, $val = false)
    {
        $this->protecteds[$nom] = array('nom' => $nom, 'val' => $val);
    }

    public function getProtected()
    {
        $code = false;

        foreach($this->protecteds as $protected) {
            if(is_array($protected['val'])) {
                $code .= '    protected $' . $protected['nom'] . ' = array();
';
            }
            elseif($protected['val']) {
                $code .= '    protected $' . $protected['nom'] . ' = true;
';
            }
            else {
                $code .= '    protected $' . $protected['nom'] . ';
';
            }
        }


        return $code;
    }

    public function getFileClassStart()
    {
        $code = <<<eof
<?php

{$this->getNamespace()}
{$this->getUses()}

eof;

        return $code;
    }

    public function getFileClassEnd()
    {
        $code = <<<eof
}

eof;

        return $code;
    }

}

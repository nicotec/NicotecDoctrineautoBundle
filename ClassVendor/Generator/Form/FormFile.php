<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;

use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FormFile extends FormGeneratorExtends {

    protected function getAction()
    {
        $suffix = 'Upload';

        if($this->getGene()->hasChampsFiles($this->getTable())) {
            foreach($this->getGene()->getChampsFiles($this->getTable()) as $champ => $options) {
                $code = false;
                $code .= <<<eof
    public \${$this->champPearMin($champ)}{$suffix};

eof;
            }
            $code .= <<<eof

    public function uploadFiles(\$root_dir_upload)
    {

eof;
            foreach($this->getGene()->getChampsFiles($this->getTable()) as $champ => $options) {
                $code .= <<<eof
        if(null !== \$this->{$this->champPearMin($champ)}{$suffix}) {
            \$name_file = '{$champ}_' . sha1(uniqid(mt_rand(), true)) . '.' . \$this->{$this->champPearMin($champ)}{$suffix}->guessExtension();

            \$this->{$this->champPearMin($champ)}{$suffix}->move(\$root_dir_upload, \$name_file);
            \$this->set{$this->camelize($this->champPearMin($champ))}(\$name_file);

            unset(\$this->{$champ}{$suffix});
        }

eof;
            }
            $code .= <<<eof
    }

eof;
        }
        else {
            $code = 'Pas de champ de fichier dans cette table';
        }


        return array(
            'help' => '',
            'fichier' => $this->getNamespaceBundleEntity() . '\Model\\' . $this->camelize($this->getTable()) . 'Base.php',
            'code' => $code,
        );
    }

}

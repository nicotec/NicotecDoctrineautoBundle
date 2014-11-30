<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;

use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormFileGerer extends FormGeneratorExtends {

    public function execute()
    {
        //namespace
//        $this->setNamespace("{$this->getNamespaceBundleController()}\Controller");

        $code = $this->getFormFile();

//            'fichier' => $this->getNamespaceBundleEntity() . '\Model\\' . $this->camelize($this->getTable()) . 'Base.php',

        return array(
            'titre' => 'FILE',
            'help' => 'File dans le {table}Base.php',
            'file' => str_replace('\\', '/', 'src/' . $this->getNamespaceBundleEntity() . '\Model\\' . $this->camelize($this->getTable()) . 'Base.php'),
            'code' => $code,
            'path_class' => $this->getPathClass(),
            'function' => __FUNCTION__,
        );
    }

    public function getFormFile()
    {
        $suffix = 'Upload';

        $code = false;

        if($this->getGene()->hasChampsFiles($this->getTable())) {
            $code .= <<<eof

    //* @ORM\HasLifecycleCallbacks
    //use Symfony\Component\Validator\Constraints as Assert;

eof;
            foreach($this->getGene()->getChampsFiles($this->getTable()) as $champ => $options) {
                $code .= <<<eof
    /**
     * @Assert\File(maxSize="6000000")
     */
    public \${$this->champPearMin($champ)}{$suffix};


eof;
            }
            $code .= <<<eof


eof;
            foreach($this->getGene()->getChampsFiles($this->getTable()) as $champ => $options) {
                $code .= <<<eof
    public function getAbsolute{$this->camelize($champ)}()
    {
        return null === \$this->{$this->champPearMin($champ)} ? null : \$this->getUploadRootDir().'/'.\$this->{$this->champPearMin($champ)};
    }

    public function getWeb{$this->camelize($champ)}()
    {
        return null === \$this->{$this->champPearMin($champ)} ? null : \$this->getUploadDir().'/'.\$this->{$this->champPearMin($champ)};
    }


eof;
            }
            $code .= <<<eof
    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.\$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
eof;
            foreach($this->getGene()->getChampsFiles($this->getTable()) as $champ => $options) {
                $code .= <<<eof
        if(null !== \$this->{$this->champPearMin($champ)}{$suffix}) {
            \$this->{$this->champPearMin($champ)} = '{$champ}_' . sha1(uniqid(mt_rand(), true)) . '.' . \$this->{$this->champPearMin($champ)}{$suffix}->guessExtension();
        }

eof;
            }
            $code .= <<<eof
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function uploadFiles()
    {

eof;
            foreach($this->getGene()->getChampsFiles($this->getTable()) as $champ => $options) {
                $code .= <<<eof
        if(null !== \$this->{$this->champPearMin($champ)}{$suffix}) {
            \$this->{$this->champPearMin($champ)}{$suffix}->move(\$this->getUploadRootDir(), \$this->{$this->champPearMin($champ)});

            unset(\$this->{$this->champPearMin($champ)}{$suffix});
        }

eof;
            }
            $code .= <<<eof
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (\$file = \$this->getAbsolutePath()) {
            unlink(\$file);
        }
    }


eof;
        }
        else {
            $code = 'Pas de champ de fichier dans cette table';
        }


        return $code;
    }

}

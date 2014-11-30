<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor;

use GeSHi;
use Symfony\Component\HttpKernel\Kernel;

// Mettre la source dans la variable $source
class GeshiColor {

    protected $kernel;
    protected $code;
    protected $resultat;
    protected $language;
    protected $is_language;
    protected $titre;
    protected $adresse;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;

        include_once $this->kernel->getRootDir() . '/../vendor/geshi/geshi.php';
    }

    public function config($source, $language)
    {
        $this->resultat = $this->language = $this->titre = $this->adresse = false;
        $this->language = $language;
        $source = html_entity_decode($source);
        $this->code = new GeSHi($source, $language);
        $this->code->set_line_style('background: #ffffff;', 'background: #ffffff;');
        $this->code->enable_keyword_links(false);
        $this->code->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 100);

        return $this;
    }

    public function getTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    public function getLanguage()
    {
        $this->is_language = true;
    }

    public function numerotation()
    {
        $this->code->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 10);
    }

    public function getResult()
    {
        if($this->titre) $this->resultat .='<div style="font-family: ;font-size: 0.85empx;color:#999999">' . $this->titre . '</div>';
        if($this->adresse) $this->resultat .='<div style="font-family: ;font-size: 0.85empx;color:#999999">#' . $this->adresse . '</div>';
        if($this->is_language) $this->resultat .='<div style="font-family: Comic Sans MS;font-size: 0.85empx;">Code : ' . $this->language;
        $this->resultat .= '<div class="code">' . $this->code->parse_code() . '</div>';
        echo '<div style="border:0px solid #cccccc;padding-left:10px;background:#ffffff;">' . $this->resultat . '</div>';
    }

}

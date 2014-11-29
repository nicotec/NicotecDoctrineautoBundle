<?php

namespace WsGene\EditBundle\ClassVendor\Generator\Form;

use WsGene\EditBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormTwigNewDev extends FormGeneratorExtends {

    public function execute()
    {
        $code = $this->getTwigNew();

        return array(
            'titre' => 'new',
            'help' => 'new simple pour twig',
            'file' => str_replace('\\', '/', 'src/' . $this->getNamespaceBundleController() . '/Resources/views/' . $this->camelize($this->getTable()) . '/' . $this->getTable() . '_new.html.twig'),
            'code' => $code,
            'path_class' => $this->getPathClass(),
            'function' => __FUNCTION__,
        );
    }

    public function getTwigNew()
    {
        $code = <<<eof
{% extends '{$this->getAlias_NSBC()}::layout.html.twig' %}

{% form_theme form '{$this->getAlias_NSBE()}:Form:fields.html.twig' %}


{% block body %}

<h2>Ajouter un enregistrement: {$this->getTable()}</h2>

{% if app.session.flashBag.has('success') %}
<div class="alert alert-success">
    {% for success in app.session.flashBag.get('success') %}
        <div>{{ success }}</div>
    {% endfor %}
</div>
{% endif %}

{% if app.session.flashBag.has('errors') %}
<div class="alert alert-error">
    {% for errors in app.session.flashBag.get('errors') %}
        <div>{{ success }}</div>
    {% endfor %}
</div>
{% endif %}

<form class="form-horizontal" novalidate="novalidate" action="{{ path('{$this->getSuffixRoute()}{$this->getTable()}_new') }}" method="post" {{ form_enctype(form) }}>
{$this->regForm()}

    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn">Enregistrer</button>
        </div>
    </div>

{{ form_end(form) }}

{% endblock %}


eof;

        return $code;
    }

}

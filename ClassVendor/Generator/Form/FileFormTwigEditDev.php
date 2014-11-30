<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;

use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormTwigEditDev extends FormGeneratorExtends {

    public function execute()
    {
        $code = $this->getTwigEdit();

        return array(
            'titre' => 'edit',
            'help' => 'edit avec toutes les lignes pour twig',
            'file' => str_replace('\\', '/', 'src/' . $this->getNamespaceBundleController() . '/Resources/views/' . $this->camelize($this->getTable()) . '/' . $this->getTable() . '_edit.html.twig'),
            'code' => $code,
            'path_class' => $this->getPathClass(),
            'function' => __FUNCTION__,
        );
    }

    public function getTwigEdit()
    {
        $code = <<<eof
{% extends '{$this->getAlias_NSBC()}::layout.html.twig' %}

{% form_theme form '{$this->getAlias_NSBE()}:Form:fields.html.twig' %}


{% block body %}

<h2>Enregistrement: {$this->getTable()}</h2>

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

<form class="form-horizontal" novalidate="novalidate" action="{{ path('{$this->getSuffixRoute()}{$this->getTable()}_edit', { 'id' : app.request.get('id') } ) }}" method="post" {{ form_enctype(form) }}>
{$this->regForm(false)}

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

<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;

use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormTwigNewEdit extends FormGeneratorExtends {

    public function execute()
    {
        $code = $this->getTwigNewEdit();

        return array(
            'titre' => 'new + edit',
            'help' => 'new + edit en une seule ligne pour twig',
            'file' => str_replace('\\', '/', 'src/' . $this->getNamespaceBundleController() . '/Resources/views/' . $this->camelize($this->getTable()) . '/' . $this->getTable() . '_edit.html.twig'),
            'code' => $code,
            'path_class' => $this->getPathClass(),
            'function' => __FUNCTION__,
        );
    }

    public function getTwigNewEdit()
    {
        $code = <<<eof
{% extends '{$this->getAlias_NSBC()}::layout.html.twig' %}

{% form_theme form '{$this->getAlias_NSBE()}:Form:fields.html.twig' %}


{% block body %}

{% if app.request.get('id') %}
<h2>Editer un enregistrement: {$this->getTable()} ({{ app.request.get('id') }})</h2>
{% else %}
<h2>Ajouter un enregistrement: {$this->getTable()}</h2>
{% endif %}

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
    {{ form_widget(form) }}

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

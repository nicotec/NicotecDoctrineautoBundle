<?php

namespace WsGene\EditBundle\ClassVendor\Generator\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use WsGene\EditBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class TwigGenerator extends FormGeneratorExtends {

    protected $request;
    protected $kernel;
    protected $bilan;

    public function __construct(Request $request, Kernel $kernel)
    {
        $this->request = $request;
        $this->kernel = $kernel;
        $this->bilan = $request->get('bilan');
    }

    public function getTwigForm($prefix_route = '_update')
    {
        $code = <<<eof
{% extends '{$this->getAlias_NSBC()}::layout.html.twig' %}

{% form_theme form '{$this->getAlias_NSBE()}:Form:fields.html.twig' %}

{% if app.session.flashBag.has('errors') %}
<div class="panel panel-success">
    <div class="panel-heading">
        <h3 class="panel-title">Erreurs</h3>
    </div>
    <div class="panel-body">
        {% for error in app.session.flashBag.get('errors') %}
        <div>{{ error }}</div>
        {% endfor %}
    </div>
</div>
{% endif %}

{% if app.session.flashBag.has('success') %}
<div class="panel panel-success">
    <div class="panel-heading">
        <h3 class="panel-title">Success</h3>
    </div>
    <div class="panel-body">
        {% for success in app.session.flashBag.get('success') %}
        <div>{{ success }}</div>
        {% endfor %}
    </div>
</div>
{% endif %}

{% block body %}
<form novalidate="novalidate" action="{{ path('{$this->getSuffixRoute()}{$this->getTable()}{$prefix_route}') }}" method="post" {{ form_enctype(form) }}>
    {{ form_widget(form) }}
</form>
{% endblock %}
eof;

        return array(
            'fichier' => $this->getNamespaceBundleController() . '\Ressources\views\Action\\' . $this->getTable() . '_insert.html.twig',
            'code' => $code,
        );
    }

    public function getTwigFormDeveloppe($prefix_route = '_update')
    {
        $code = <<<eof
{% extends '{$this->getAlias_NSBC()}::layout.html.twig' %}

{% form_theme form '{$this->getAlias_NSBE()}:Form:fields.html.twig' %}


{% block body %}
{% if(app.request.get('id')) %}
Ajouter {$this->getTable()}
{% else %}
Editer {$this->getTable()}
{% endif %}

{% if app.session.flashBag.has('errors') %}
<div class="panel panel-success">
    <div class="panel-heading">
        <h3 class="panel-title">Erreurs</h3>
    </div>
    <div class="panel-body">
        {% for error in app.session.flashBag.get('errors') %}
        <div>{{ error }}</div>
        {% endfor %}
    </div>
</div>
{% endif %}

{% if app.session.flashBag.has('success') %}
<div class="panel panel-success">
    <div class="panel-heading">
        <h3 class="panel-title">Success</h3>
    </div>
    <div class="panel-body">
        {% for success in app.session.flashBag.get('success') %}
        <div>{{ success }}</div>
        {% endfor %}
    </div>
</div>
{% endif %}



{#insert#}
<form novalidate="novalidate" action="{{ path('{$this->getSuffixRoute()}{$this->getTable()}_insert', { 'id' : app.request.get('id') } ) }}" method="post" {{ form_enctype(form) }}>
{$this->regForm()}
    {{ form_rest(form) }}
</form>

{#update#}
{% block body %}
<form novalidate="novalidate" action="{{ path('{$this->getSuffixRoute()}{$this->getTable()}{$prefix_route}', { 'id' : app.request.get('id') } ) }}" method="post" {{ form_enctype(form) }}>
{$this->regForm(false)}
    {{ form_rest(form) }}
</form>



{% endblock %}
eof;

        return array(
            'help' => '',
            'fichier' => $this->getNamespaceBundleController() . '\Ressources\Action\views\Action\\' . $this->getTable() . '_insert.html.twig',
            'code' => $code,
        );
    }

    public function getTwigFormTheme()
    {
        $code = <<<eof
{% block field_widget %}
{% spaceless %}
{% set type = type|default('text') %}

<div class="control-group">
    {% if type == 'text' %}
    <label class="control-label">{{ label|capitalize }} :</label>
    <div class="controls">
        {{ form_errors(form) }}
        {{ form_widget(form) }}
    </div>
    {% elseif type == 'file' %}
    {% set img = img|default(null) %}
    <label class="control-label">{{ label|capitalize }} :</label>
    <div class="controls">
        {{ form_errors(form) }}
        {{ form_widget(form) }}
        {% if img %}
        <p>
            <img src="{{ img }}" alt="{{ img }}" style="width: 200px;">
        </p>
        {% endif %}
    </div>
    {% else %}
    <p class="_100 small">
        type non gérer
    </p>
    {% endif %}
</div>

{% endspaceless %}
{% endblock field_widget %}


{% block textarea_widget %}
{% spaceless %}
{% set type = type|default('text') %}
{% set grid_num = grid_num|default(6) %}
<div class="control-group">
    <label class="control-label">{{ label|capitalize }} :</label>
    <div class="controls">
        {{ form_errors(form) }}
        <textarea  {{ block('widget_attributes') }} rows="6" style="width: 400px" >{% if value is not empty %}{{ value }}{% endif %}</textarea>
    </div>
</div>
{% endspaceless %}
{% endblock textarea_widget %}






{% block choice_widget %}
{% spaceless %}
{% set type = type|default('choice') %}
{% set grid_num = grid_num|default(5) %}

<div class="control-group">
    <label class="control-label">{{ label|capitalize }} :</label>
    <div class="controls">
        {{ form_errors(form) }}
        <select {{ block('widget_attributes') }}{% if multiple %} multiple="multiple"{% endif %}>
            {% if empty_value is not none %}
            <option value="">{{ empty_value|trans }}</option>
            {% endif %}
            {% if preferred_choices|length > 0 %}
            {% set options = preferred_choices %}
            {{ block('widget_choice_options') }}
            {% if choices|length > 0 and separator is not none %}
            <option disabled="disabled">{{ separator }}</option>
            {% endif %}
            {% endif %}
            {% set options = choices %}
            {{ block('widget_choice_options') }}
        </select>
    </div>
</div>


{% endspaceless %}
{% endblock choice_widget %}




{# block form_widget_simple %}
{% set type = type|default('text') %}
<input type="{{ type }}" {{ block('widget_attributes') }} {% if value is not empty %}value="{{ value }}" {% endif %}/>
{% endblock form_widget_simple #}




{% block password_widget %}
{% spaceless %}
{% set type = type|default('password') %}
{{ block('field_widget') }}
{% endspaceless %}
{% endblock password_widget %}

eof;

        return array(
            'help' => '',
            'fichier' => $this->getNamespaceBundleController() . '\Ressources\Action\views\Form\\fields.html.twig',
            'code' => $code,
        );
    }

}


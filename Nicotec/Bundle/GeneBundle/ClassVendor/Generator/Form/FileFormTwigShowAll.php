<?php

namespace WsGene\EditBundle\ClassVendor\Generator\Form;

use WsGene\EditBundle\ClassVendor\Generator\Form\FormGeneratorExtends;

class FileFormTwigShowAll extends FormGeneratorExtends {

    public function execute()
    {
        $code = $this->getTwigShowAll();

        return array(
            'titre' => 'new',
            'help' => 'new simple pour twig',
            'file' => str_replace('\\', '/', 'src/' . $this->getNamespaceBundleController() . '/Resources/views/' . $this->camelize($this->getTable()) . '/' . $this->getTable() . 's.html.twig'),
            'code' => $code,
            'path_class' => $this->getPathClass(),
            'function' => __FUNCTION__,
        );
    }

    public function getTwigShowAll()
    {
        $code = <<<eof
{% extends '{$this->getAlias_NSBC()}::layout.html.twig' %}


{% block body %}

<h1>Liste des {$this->getTable()}s</h1>

<table class="table table-bordered">
   <tr>

eof;

        foreach($this->getGene()->getChamps($this->getTable()) as $champ => $value) {
            $code .= "        <th>{$champ}</th>
";
        }

        $code .= <<<eof
        <th>Action</th>
   </tr>
    {% for {$this->getTable()} in {$this->getTable()}s %}
   <tr>

eof;

        foreach($this->getGene()->getChamps($this->getTable()) as $champ => $value) {
            if($this->getGene()->hasJoin($this->getTable(), $champ)) {
                $j = $this->getGene()->getJoin($this->getTable(), $champ);
                $code .= "        <td>{{ {$this->getTable()}.{$this->champPearMin($j['table_etrangere'])}.toString }}</td>
";
            }
            elseif($this->getGene()->hasChampFile($this->getTable(), $champ)) {
                $code .= "        <td align=\"center\"><img src=\"{{ asset({$this->getTable()}.web{$this->champPearMin($champ)}) }}\" style=\"max-width: 50px;max-height: 50px;\"/></td>
";
            }
            else {
                $code .= "        <td>{{ {$this->getTable()}.{$this->champPearMin($champ)} }}</td>
";
            }
        }

        $code .= <<<eof
        <td width="110">
            {#<a data-original-title="Voir" rel="tooltip" data-placement="top" class="btn btn-small" href="{{ path('admin.{$this->getTable()}_show', { 'id' : {$this->getTable()}.id } ) }}"><i class="icon-eye-open"></i></a>#}
            <a data-original-title="Editer" rel="tooltip" data-placement="top" class="btn btn-small" href="{{ path('admin.{$this->getTable()}_edit', { 'id' : {$this->getTable()}.id } ) }}"><i class="icon-edit"></i></a>
            <a data-original-title="Supprimer (après confirmation)" rel="tooltip" data-placement="top" class="btn btn-small enre_delete" href="{{ path('admin.{$this->getTable()}_delete', { 'id' : {$this->getTable()}.id } ) }}"><i class="icon-remove"></i></a>
        </td>
    </tr>
    {% endfor %}
</table>

<br>
<a href="{{ path('admin.{$this->getTable()}_new') }}" class="btn btn-success">Ajouter {$this->getTable()}</a>


{% endblock %}


eof;

        return $code;
    }

}

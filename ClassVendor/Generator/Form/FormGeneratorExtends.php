<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Entity\FormatGenerator;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\GeneFiltre;

class FormGeneratorExtends Extends FormatGenerator {

    protected $request, $session, $kernel, $gene_filtre = array(), $path_class, $code, $bilan, $gene;

    public function __construct(Request $request, Kernel $kernel)
    {
        $this->request = $request;
        $this->session = $request->getSession();
        $this->kernel = $kernel;
    }

    public function setPathClass($path_class)
    {
        $this->path_class = $path_class;
    }

    public function getPathClass()
    {
        return $this->path_class;
    }

//    public function setBilan($bilan)
//    {
//        $this->bilan = $bilan;
//    }
//
//    public function getBilan()
//    {
//        return $this->bilan;
//    }

    public function setGene(GeneFiltre $gene)
    {
        $this->gene = $gene;
    }

    public function getGene()
    {
        return $this->gene;
    }

    public function getAlias_NSBC()
    {
        return str_replace('\\', '', $this->session->get('namespace_bundle_controller'));
    }

    public function getAlias_NSBE()
    {
        return str_replace('\\', '', $this->session->get('namespace_bundle_entity'));
    }

    public function getTableCamelize()
    {
        return $this->camelize($this->getTable());
    }

    //---------------------------------------GETTERS SESSION--------------
    public function getTable()
    {
        return $this->session->get('table');
    }

    public function getSuffixRoute()
    {
        return $this->session->get('suffix_route');
    }

    public function getNamespaceBundleController()
    {
        return $this->session->get('namespace_bundle_controller');
    }

    public function getNamespaceBundleEntity()
    {
        return $this->session->get('namespace_bundle_entity');
    }

    public function regAdd($virgule = false, $joins_reverses = false)
    {
        $action = false;

        if($this->getGene()->hasChamps($this->getTable())) {
            foreach($this->getGene()->getChamps($this->getTable()) as $champ => $options) {
                if($champ == 'id' || $champ == 'created' || $champ == 'updated' || $champ == 'position') {
                    //pas de code
                }
                else {
//                var_dump($this->getGene()->getType($this->getTable(), $champ));
                    $info_type = $this->getGene()->getType($this->getTable(), $champ);

                    if($champ == 'password') {
                        $action .= <<<eof
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => array('required' => true),
                'first_options' => array('label' => 'Mot de passe', ),
                'second_options' => array('label' => 'Mot de passe (validation)'),
                'first_name'=> 'password',
                'second_name'=> 'password2',
            ))

eof;
                    }
                    elseif($this->getGene()->hasJoin($this->getTable(), $champ)) {
                        $jbn = $this->getGene()->getJoin($this->getTable(), $champ);
                        $action .= <<<eof
            ->add('{$this->champPearMin($jbn['table_etrangere'])}', 'entity', array(
                'class' => '{$this->getAlias_NSBE()}:{$this->camelize($jbn['table_etrangere'])}',
                'property' => 'toString',
                'required' => '{$jbn['infos']['null']}',
                'label' => '{$this->label($jbn['table_etrangere'])}',
                'empty_value' => '--Sélectionner--',
//                'query_builder' => function(EntityRepository \$er) {
//                    return \$er->createQueryBuilder('a')
//                        ->where('a.xxx = 0')
//                    ;
//                },
            ))

eof;
                    }
                    elseif($this->getGene()->hasJoinSelfReferencing($this->getTable(), $champ)) {
                        $jbn = $this->getGene()->getJoinSelfReferencing($this->getTable(), $champ);
                        $action .= <<<eof
            ->add('{$this->champPearMin($jbn['champ_children'])}', 'entity', array(
                'class' => '{$this->getAlias_NSBE()}:{$this->camelize($jbn['table_etrangere'])}',
                'property' => 'toString',
                'required' => '{$jbn['infos']['null']}',
                'label' => '{$this->label($jbn['champ_children'])}',
                'empty_value' => '--Sélectionner--',
//                'query_builder' => function(EntityRepository \$er) {
//                    return \$er->createQueryBuilder('a')
//                        ->where('a.xxx = 0')
//                    ;
//                },
            ))

eof;
                    }
                    elseif(substr($champ, 0, 5) == 'file_') {
                        $action .= <<<eof
            ->add('{$this->champPearMin($champ)}Upload', 'file', array('label' => '{$this->label($champ)}'))

eof;
                    }
                    elseif($champ == 'civilite') {
                        $action .= <<<eof
            ->add('{$this->champPearMin($champ)}', 'choice', array('label' => '{$this->label($champ)}', 'choices' => array('1' => 'Mr', '2' => 'Mme', '3' => 'Mll'),))

eof;
                    }
                    elseif($champ == 'email' || $champ == 'mail') {
                        $action .= <<<eof
            ->add('{$this->champPearMin($champ)}', 'email', array('label' => '{$this->label($champ)}'))

eof;
                    }
                    elseif($info_type == 'datetime') {
                        $action .= <<<eof
            ->add('{$this->champPearMin($champ)}', 'date', array(
              'label' => '{$this->label($champ)}',
//              'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
            ))

eof;
                    }
                    elseif($info_type == 'string') {
                        $action .= <<<eof
            ->add('{$this->champPearMin($champ)}', 'text', array('label' => '{$this->label($champ)}'))

eof;
                    }
                    elseif($info_type == 'boolean') {
                        $action .= <<<eof
            ->add('{$this->champPearMin($champ)}', 'choice', array('label' => '{$this->label($champ)}', 'choices' => array('1' => 'oui', '0' => 'non'),))

eof;
                    }
                    elseif($info_type == 'text') {
                        $action .= <<<eof
            ->add('{$this->champPearMin($champ)}', 'textarea', array('label' => '{$this->label($champ)}'))

eof;
                    }
                    elseif($info_type == 'decimal') {
                        $action .= <<<eof
            ->add('{$this->champPearMin($champ)}', 'number', array('label' => '{$this->label($champ)}'))

eof;
                    }
                    else {
                        $action .= <<<eof
            ->add('{$this->champPearMin($champ)}', 'text', array('label' => '{$this->label($champ)}'))

eof;
                    }
                }
            }
        }
        else {
//            echo 'erreur ?? ' . __METHOD__;
        }

        if($joins_reverses) {
            if($this->getGene()->hasJoinsReverses($this->getTable(), $champ)) {
                foreach($this->getGene()->getJoinsReverses($this->getTable()) as $table => $configs) {
                    $action .= <<<eof
            ->add('{$this->champPearMin($table)}', 'collection', array(
                'type' => new {$this->camelize($table)}Type,
//                    'prototype' => true,
                'allow_add' => false
            ))

eof;
                }
            }
        }


        if($virgule) {
            $action .= <<<eof
        ;

eof;
        }
        return $action;
//            ->add('etat', 'choice', array(
//                'label' => 'etat',
//                'choices' => array('1' => 'Actif', '2' => 'Inactif'),
//                'required' => false,
//            ))
//            ->add('status', 'choice', array(uploadFiles
//                'label' => 'status',
//                'choices' => array('1' => 'Administrateur', '2' => 'Utilisateur'),
//                'required' => false,
//            ))
    }

    public function regForm($insert = true)
    {
        $action = false;

        if($this->getGene()->hasChamps($this->getTable())) {
            foreach($this->getGene()->getChamps($this->getTable()) as $champ => $options) {
                if($champ == 'id' || $champ == 'salt' || $champ == 'created' || $champ == 'updated' || $champ == 'position') {
                    //pas de code
                }
                elseif($champ == 'password') {
                    $action .= <<<eof
    {{ form_row(form.password.password) }}
    {{ form_row(form.password.password2) }}

eof;
                }
                elseif($this->getGene()->isChampFile($champ)) {
                    if($insert) {
                        $action .= <<<eof
    {{ form_row(form.{$this->champPearMin($champ)}Upload) }}

eof;
                    }
                    else {
                        $action .= <<<eof
    {{ form_row(form.{$this->champPearMin($champ)}Upload, { 'web_path' :  {$this->getTable()}.web{$this->camelize($champ)} }) }}

eof;
                    }
                }
                elseif($this->getGene()->hasJoinSelfReferencing($this->getTable(), $champ)) {
                    $jsr = $this->getGene()->getJoinSelfReferencing($this->getTable(), $champ);
                    $action .= <<<eof
    {{ form_row(form.{$this->champPearMin($jsr['champ_children'])}) }}

eof;
                }
                elseif($this->getGene()->hasJoin($this->getTable(), $champ)) {
//                echo '<br>' . $champ;
                    $jsr = $this->getGene()->getJoin($this->getTable(), $champ);
//                var_dump($jsr);
                    $action .= <<<eof
    {{ form_row(form.{$this->champPearMin($jsr['table_etrangere'])}) }}

eof;
                }
                else {
                    $action .= <<<eof
    {{ form_row(form.{$this->champPearMin($champ)}) }}

eof;
                }
            }
        }
        else {
//            echo 'erreur ?? ' . __METHOD__;
        }

        return $action;
    }

    public function regDoctrineSet()
    {
        $action = false;

        foreach($this->getGene()->getChamps($this->getTable()) as $champ => $options) {
            if($champ == 'id') {
                //pas de code
            }
            else {
                if($this->getGene()->hasJoin($this->getTable(), $champ)) {
                    $jbn = $this->getGene()->getJoin($this->getTable(), $champ);

                    $action .= <<<eof
    \${$this->getTable()}->set{$this->camelize($jbn['table_etrangere'])}();

eof;
                }
                else {
                    $action .= <<<eof
    \${$this->getTable()}->set{$this->camelize($champ)}();

eof;
                }
            }
        }

        return $action;
    }

    public function regTableTh()
    {
        $action = false;

        foreach($this->getGene()->getChamps($this->getTable()) as $champ => $options) {
            if($champ == 'id') {
                //pas de code
            }
            else {
                $info_type = $this->getGene()->getChampType($this->getTable(), $champ);

                if($this->getGene()->hasJoin($this->getTable(), $champ)) {
                    $jbn = $this->getGene()->getJoin($this->getTable(), $champ);
                    $champ = ucfirst(str_replace('_', '', trim($jbn['table_etrangere'])));

                    $action .= <<<eof
            <th>{$champ}</th>

eof;
                }
                elseif($info_type == 'boolean') {
                    $champ = ucfirst(str_replace(array('is_', '_'), array('', ' '), trim($champ)));
                    $action .= <<<eof
            <th>{$champ}</th>

eof;
                }
                else {
                    $champ = ucfirst(str_replace('_', '', trim($champ)));
                    $action .= <<<eof
            <th>{$champ}</th>

eof;
                }
            }
        }

        return $action;
    }

    public function regTableTd()
    {
        $action = false;

        foreach($this->getGene()->getChamps($this->getTable()) as $champ => $options) {
            if($champ == 'id') {
                //pas de code
            }
            else {
                $info_type = $this->getGene()->getChampType($this->getTable(), $champ);

                if($this->getGene()->hasJoin($this->getTable(), $champ)) {
                    $jbn = $this->getGene()->getJoin($this->getTable(), $champ);

                    $action .= <<<eof
            <td>{{ {$this->getTable()}.{$this->champPearMin($jbn['table_etrangere'])}.toString }}</td>

eof;
                }
                elseif($info_type == 'boolean') {
                    $action .= <<<eof
            <td>{% if {$this->getTable()}.{$this->champPearMin($champ)} %}oui{% else %}non{% endif %}</td>

eof;
                }
                elseif($info_type == 'datetime') {
                    $action .= <<<eof
            <td>{{ {$this->getTable()}.{$this->champPearMin($champ)}|date('Y-m-d') }}</td>

eof;
                }
                else {
                    $action .= <<<eof
            <td>{{ {$this->getTable()}.{$this->champPearMin($champ)} }}</td>

eof;
                }
            }
        }

        return $action;
    }

}

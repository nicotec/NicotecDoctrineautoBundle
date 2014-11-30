<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Entity;

abstract class SetterGenerator extends GetterGenerator
{

    protected function setDeclarationId($table, $champ, $configs)
    {
        $this->_gene[$table]['id'][$champ] = $configs;
    }

    protected function setJoin_Any($table, $champ, $configs)
    {
        $this->_gene[$table]['normal'][$champ] = $configs;
    }

//    protected function setDeclarationClef($table, $champ, $configs)
//    {
//        $this->_gene[$table]['clef'][$champ] = $configs;
//    }

    protected function setJoin_ManyToOne_OneToMany($table, $champ, $table_etrangere, $configs)
    {
        $configs['table_etrangere'] = $table_etrangere;

        $this->_gene[$table]['join'][$table_etrangere] = $configs;
        $this->_gene[$configs['prefix_table'] . $table_etrangere]['join_reverse'][$table] = $configs;
    }

    protected function setJoin_ManyToOne_OneToMany_Add($table, $champ, $table_etrangere, $table_reduc, $configs)
    {
        $configs['table_etrangere'] = $table_etrangere;
        $configs['table_reduc'] = $table_reduc;
        $configs['table'] = $table;

        echo '<br>---------' . $table . '--' . $table_reduc . '--' . $table_etrangere;
        if(isset($this->_gene[$table]['join_add'][$table_etrangere])) {
            throw new Exception('existe déjà - ' . $table . '--');
        }

        $this->_gene[$table]['join_add'][$table_etrangere] = $configs;
        $this->_gene[$configs['prefix_table'] . $table_reduc]['join_add_reverse'][] = $configs;
    }

    protected function setDeclarationSelfReferencing($table, $champ, $table_etrangere, $configs)
    {
        echo $table . '--' . $champ;
        die;
        $configs['table_etrangere'] = $table;
        $configs['champ_children'] = $table_etrangere;
        //$this->_gene[$table]['join_sr'][substr($champ_name, 0, -3)] = $configs;
        //$this->_gene[$table]['join_reverse'][$champ_name . 'Children'] = $configs;
        //echo '<br>' . $table . '--' . $champ;
        $this->_gene[$table]['join_sr'][$table_etrangere] = $configs;
        $this->_gene[$table]['join_reverse_sr'][$table_etrangere] = $configs;
        $this->setJoin_Any($table, $champ, $configs);
    }

    //'user_group', 'user_id'
    protected function setJoin_ManyToMany($table, $champ, $configs)
    {
        //                'user_group' 'user_id'
        $this->manyToMany[$table][] = $champ;
    }

    //'user_group', 'user_id', 'user'
    protected function initializeDeclarationManyToMany()
    {
        if(isset($this->manyToMany)) {
            foreach($this->manyToMany as $table => $configs) {
                if(count($configs) == 2) {
                    $table_1 = substr($configs[0], 0, -3);
                    $table_2 = substr($configs[1], 0, -3);

                    $this->_gene[$table_1]['join_many'][$table]['num'] = '1';
                    $this->_gene[$table_1]['join_many'][$table]['table_liaison'] = $table;
                    $this->_gene[$table_1]['join_many'][$table]['table_etrangere'] = $table_2;

                    $this->_gene[$table_2]['join_many'][$table]['num'] = '2';
                    $this->_gene[$table_2]['join_many'][$table]['table_liaison'] = $table;
                    $this->_gene[$table_2]['join_many'][$table]['table_etrangere'] = $table_1;

                    $this->_gene[$table_1]['join_reverse_many'][$table_2] = array();
                    $this->_gene[$table_2]['join_reverse_many'][$table_1] = array();
                }
                else {
                    $this->getLogger()->err('Exception ' . count($configs) . ' clef étrangères pour une liaison ManyToMany', array('table' => $table));
                }
            }
        }
    }

    protected function setDeclarationCreated($table, $champ)
    {
        $this->_gene[$table]['created'] = $champ;
    }

    protected function setDeclarationUpdated($table, $champ)
    {
        $this->_gene[$table]['updated'] = $champ;
    }

    protected function setDeclarationTree($table, $champ)
    {
        if($champ == 'lft' || $champ == 'left') {
            $this->_gene[$table]['tree']['left'] = $champ;
        }
        if($champ == 'rgt' || $champ == 'right') {
            $this->_gene[$table]['tree']['right'] = $champ;
        }
        if($champ == 'lvl' || $champ == 'level') {
            $this->_gene[$table]['tree']['level'] = $champ;
        }
        if($champ == 'root_id' || $champ == 'root') {
            $this->_gene[$table]['tree']['root'] = $champ;
        }
        if($champ == 'parent_id') {
            $this->_gene[$table]['tree']['parent'] = $champ;
        }

        if(isset($this->_gene[$table]['tree']['left']) && isset($this->_gene[$table]['tree']['right']) && isset($this->_gene[$table]['tree']['level']) && isset($this->_gene[$table]['tree']['root']) && isset($this->_gene[$table]['tree']['parent'])) {
            $this->_gene[$table]['tree']['is'] = true;
        }
    }

    protected function setDeclarationSortable($table, $champ)
    {
        if($champ == 'position') {
            $this->_gene[$table]['sortable']['position'] = $champ;
        }
        if($champ == 'category') {
            $this->_gene[$table]['sortable']['category'] = $champ;
        }

        if(isset($this->_gene[$table]['sortable']['position'])) {
            $this->_gene[$table]['sortable']['is'] = true;
        }
    }

}

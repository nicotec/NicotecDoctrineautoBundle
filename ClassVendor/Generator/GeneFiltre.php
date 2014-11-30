<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator;

class GeneFiltre {

    protected $gene, $message;

    public function __construct($gene = array())
    {
        $this->gene = $gene;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getType($table, $champ)
    {
//        $this->message .= '<br>' . $table . '--' . $champ;
//        var_dump($this->gene);die;
        return $this->gene[$table]['normal'][$champ]['infos']['type'];
    }

    public function hasLifecycleCallbacks($table)
    {
        if(isset($this->gene[$table]['updated']) && isset($this->gene[$table]['created'])) {
            return true;
        }

        return false;
    }

    public function getCreated($table)
    {
        return $this->gene[$table]['created'];
    }

    public function getUpdated($table)
    {
        return $this->gene[$table]['updated'];
    }

    /**
     * Id
     */
    public function hasId($table)
    {
        return isset($this->gene[$table]['id']);
    }

    public function getIds($table)
    {
        return $this->gene[$table]['id'];
    }

    /**
     * Champ
     */
    public function hasChamp($table, $champ)
    {
        return isset($this->gene[$table]['normal'][$champ]);
    }

    public function hasChamps($table)
    {
        return isset($this->gene[$table]['normal']);
    }

    public function getChamps($table)
    {
        return $this->gene[$table]['normal'];
    }

    public function getChampType($table, $champ)
    {
        return $this->gene[$table]['normal'][$champ]['infos']['type'];
    }

    /**
     * Joins
     */
    public function hasJoins($table)
    {
        return isset($this->gene[$table]['join']);
    }

    public function getJoins($table)
    {
        return $this->gene[$table]['join'];
    }

    public function hasJoin($table, $champ)
    {
        return isset($this->gene[$table]['join'][str_replace('_id', '', $champ)]);
    }

    public function getJoin($table, $champ)
    {
        return $this->gene[$table]['join'][str_replace('_id', '', $champ)];
    }

    /**
     * JoinsReverses
     */
    public function hasJoinsReverses($table)
    {
        return isset($this->gene[$table]['join_reverse']);
    }

    public function getJoinsReverses($table)
    {
        return $this->gene[$table]['join_reverse'];
    }

    public function hasJoinReverse($table, $champ)
    {
        return isset($this->gene[$table]['join_reverse'][str_replace('_id', '', $champ)]);
    }

    public function getJoinReverse($table, $champ)
    {
        return $this->gene[$table]['join_reverse'][str_replace('_id', '', $champ)];
    }

    /**
     * Joins Add
     */
    public function hasJoinsAdd($table)
    {
        return isset($this->gene[$table]['join_add']);
    }

    public function getJoinsAdd($table)
    {
        return $this->gene[$table]['join_add'];
    }

    public function hasJoinAdd($table, $champ)
    {
        return isset($this->gene[$table]['join_add'][str_replace('_id', '', $champ)]);
    }

    public function getJoinAdd($table, $champ)
    {
        return $this->gene[$table]['join_add'][str_replace('_id', '', $champ)];
    }

    /**
     * JoinsAddReverses
     */
    public function hasJoinsAddReverses($table)
    {
        return isset($this->gene[$table]['join_add_reverse']);
    }

    public function getJoinsAddReverses($table)
    {
        return $this->gene[$table]['join_add_reverse'];
    }

    public function hasJoinAddReverse($table, $champ)
    {
        return isset($this->gene[$table]['join_add_reverse'][str_replace('_id', '', $champ)]);
    }

    public function getJoinAddReverse($table, $champ)
    {
        return $this->gene[$table]['join_add_reverse'][str_replace('_id', '', $champ)];
    }

    /**
     * JoinsManys
     */
    public function hasJoinsManys($table)
    {
        return isset($this->gene[$table]['join_many']);
    }

    public function getJoinsManys($table)
    {
        return $this->gene[$table]['join_many'];
    }

    /**
     * JoinsReverseManys
     */
    public function hasJoinsReverseManys($table)
    {
        return isset($this->gene[$table]['join_reverse_many']);
    }

    public function getJoinsReverseManys($table)
    {
        return $this->gene[$table]['join_reverse_many'];
    }

    /**
     * JoinSelfReferencings
     */
    public function hasJoinSelfReferencings($table)
    {
        return isset($this->gene[$table]['join_sr']);
    }

    public function hasJoinSelfReferencing($table, $champ)
    {
        return isset($this->gene[$table]['join_sr'][str_replace('_id', '', $champ)]);
    }

    public function getJoinSelfReferencings($table)
    {
        return $this->gene[$table]['join_sr'];
    }

    public function getJoinSelfReferencing($table, $champ)
    {
        return $this->gene[$table]['join_sr'][str_replace('_id', '', $champ)];
    }

    /**
     * JoinReverseSelfReferencings
     */
    public function hasJoinSelfReferencingReverses($table)
    {
        return isset($this->gene[$table]['join_reverse_sr']);
    }

    public function getJoinSelfReferencingReverses($table)
    {
        return $this->gene[$table]['join_reverse_sr'];
    }

    //////////////////////////////////set
    //////////////////////////////////set
    //////////////////////////////////set

    public function hasTree($table)
    {
        return isset($this->gene[$table]['tree']['is']);
    }

    public function getTrees($table)
    {
        return $this->gene[$table]['tree'];
    }

    public function hasSortable($table)
    {
        return isset($this->gene[$table]['sortable']['is']);
    }

    public function hasSortablePosition($table)
    {
        return isset($this->gene[$table]['sortable']['position']);
    }

    public function hasSortableCategory($table)
    {
        return isset($this->gene[$table]['sortable']['category']);
    }

    public function hasChampsFiles($table)
    {
        if(isset($this->gene[$table]['normal'])) {
            foreach($this->gene[$table]['normal'] as $champ => $options) {
                if(substr($champ, 0, 5) == 'file_') {
                    return true;
                }
            }
        }
        else {
            $this->message .= '<br>Pas de champs ?? ' . __METHOD__;
        }

        return false;
    }

    public function hasChampFile($table, $champ)
    {
        if(substr($champ, 0, 5) == 'file_') {
            return true;
        }

        return false;
    }

    public function getChampsFiles($table)
    {
        $champs = array();
        foreach($this->gene[$table]['normal'] as $champ => $options) {
            if(substr($champ, 0, 5) == 'file_') {
                $champs[$champ] = $options;
            }
        }

        return $champs;
    }

    public function isChampFile($champ)
    {
        if(substr($champ, 0, 5) == 'file_') {
            return true;
        }

        return false;
    }

}

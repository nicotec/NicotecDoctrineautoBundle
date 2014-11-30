<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Entity;

use Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\GeneFiltre;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Register;

class EntityGenerator extends SetterGenerator
{

    protected $dir_root = false; //root du projet sf2 -> /src/
    protected $tree = array('lft', 'rgt', 'lvl', 'level', 'root_id', 'parent_id');
    protected $sortable = array('position45', 'category');

    /**
     * Utile lorsqu'il existe un prefix de table mais pas sur les clefs etrangeres
     * Prefix des table: ex(bo_) 'bo_client'
     * Cependant les clef etrangères reste normal -> 'adresse'.'client_id'
     * Gère la presence ou on du préfix
     */
    protected $prefix_table = false;

    /**
     * prefix de l'orm (peut etre vide)
     */
    protected $prefix = 'ORM\\';
    protected $base_name = false;
    protected $_code_entity;
    protected $code_entity = array();
    protected $_gene = array();
    protected $gene_filtre;
    protected $_code_entity_base;
    protected $code_entity_base = array();
    protected $_code_repository;
    protected $code_repository = array();
    protected $log;
    protected $is_file_defaut;
    protected $bilan_errs = array();

    /**
     * @return Register
     */
    public function getRegister()
    {
        return $this->register;
    }

    public function setIsFileContent($is_file_defaut = false)
    {
        $this->is_file_defaut = $is_file_defaut;
    }

    public function getIsFileContent()
    {
        return $this->is_file_defaut;
    }

    public function setGene()
    {
        $this->gene_filtre = new GeneFiltre($this->_gene);
    }

    public function getCodeEntity()
    {
        return $this->code_entity;
    }

    public function getCodeEntityBase()
    {
        return $this->code_entity_base;
    }

    public function getCodeRepository()
    {
        return $this->code_repository;
    }

    public function getBase()
    {
        return $this->register->getBase();
    }

    /**
     * Change le prefix de l'orm dans les class Entity (par defaut: ORM\\)
     * @param type $prefix_orm
     */
    public function setPrefix($prefix_orm)
    {
        $this->prefix = $prefix_orm;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->log;
    }

    public function getBilanErrs()
    {
        return $this->bilan_errs;
    }

    public function dispatch($is_assemblage = false)
    {
        $this->register->setShowTable();
        $this->_bilan = $this->register->getBilan();
        $this->_clef_primaire = $this->register->getClefPrimaire();

        foreach($this->_bilan as $table => $champ) {

            //echo '<br>' . $table . '--' . count($champ);
            foreach($champ as $infos) {
                $configs = array();
                $champ_name = $infos['Field'];

                //echo '<br><b>'.$table.'</b>--'.$infos['Field'].'--'.$infos['Key'];
                //si la table a une clef primaire
                if($this->_clef_primaire[$table]) {

                    if($champ_name == 'created' || $champ_name == 'created_at') {
                        $this->setDeclarationCreated($table, $champ_name);
                    }
                    if($champ_name == 'updated' || $champ_name == 'updated_at') {
                        $this->setDeclarationUpdated($table, $champ_name);
                    }

                    if(in_array($champ_name, $this->sortable)) {
                        $this->setDeclarationSortable($table, $champ_name);
//                        $configs['no_set'] = true;
                        continue;
                    }
                    //var_dump($this->tree);
                    //die;
                    if(in_array($champ_name, $this->tree)) {
//                        echo $champ_name . '/';
                        $this->setDeclarationTree($table, $champ_name);
                        continue;
                    }

                    //Type
                    if(substr($infos['Type'], 0, 7) == 'varchar') {
                        $configs['infos']['type'] = 'string';
                        $configs['length'] = str_replace(array('varchar(', ')'), '', $infos['Type']);
                    }
                    elseif(substr($infos['Type'], 0, 3) == 'int' || substr($infos['Type'], 0, 9) == 'mediumint') {
                        $configs['infos']['type'] = 'integer';
                    }
                    elseif(substr($infos['Type'], 0, 8) == 'smallint') {
                        $configs['infos']['type'] = 'smallint';
                    }
                    elseif(substr($infos['Type'], 0, 6) == 'bigint') {
                        $configs['infos']['type'] = 'bigint';
                    }
                    elseif($infos['Type'] == 'tinyint(1)' || $infos['Type'] == 'boolean') {
                        $configs['infos']['type'] = 'boolean';
//                        echo ('<br>'.$champ_name);
                    }
                    elseif(substr($infos['Type'], 0, 7) == 'tinyint') { //! après tinyint(1)
                        $configs['infos']['type'] = 'integer';
                    }
                    elseif(substr($infos['Type'], 0, 7) == 'decimal') {
                        $configs['infos']['type'] = 'decimal';
                    }
                    elseif(substr($infos['Type'], 0, 6) == 'double' || substr($infos['Type'], 0, 5) == 'float') {
                        $configs['infos']['type'] = 'decimal';
                    }
                    elseif($infos['Type'] == 'text' || $infos['Type'] == 'mediumtext' || $infos['Type'] == 'longtext' || $infos['Type'] == 'tinytext') {
                        $configs['infos']['type'] = 'text';
                    }
                    elseif($infos['Type'] == 'date' || $infos['Type'] == 'datetime' || $infos['Type'] == 'timestamp') {
                        $configs['infos']['type'] = 'datetime';
                    }
                    elseif($infos['Type'] == 'time') {
                        $configs['infos']['type'] = 'time';
                    }
                    else {
                        $this->getLogger()->err('Type non supporte ' . $table . '-' . $champ_name . '-' . $infos['Type']);
                        die('$infos[\'Type\'] non gerer : ' . $infos['Type']);
                    }

                    //Null
                    if($infos['Null'] == 'NO') {
                        $configs['infos']['null'] = 'false';
                    }
                    else {
                        $configs['infos']['null'] = 'true';
                    }

                    if(strlen($infos['Default']) > 0) {
                        $configs['infos']['default'] = $infos['Default'];
                    }
                    else {
                        $configs['infos']['default'] = false;
                    }


                    //id
                    if($champ_name == 'id') {
                        if($infos['Key'] == 'PRI' && $infos['Extra'] == 'auto_increment') {
                            $this->setDeclarationId($table, $champ_name, $configs);
                            continue;
                        }
                        else {
                            $mss = 'Erreur clef primaire (doit-être auto_increment) ' . $table . '-' . $champ_name . '-' . $infos['Type'];
                            $this->getLogger()->err($mss);
                            throw new Exception($mss);
                        }
                    }

                    //jointure
                    elseif(substr($champ_name, -3) == '_id' && $champ_name != 'num_id' && $champ_name != 'root_id' && $champ_name != 'connexion_id') {
                        $table_etrangere = substr($champ_name, 0, -3);
                        if($this->prefix_table && isset($this->_bilan[$this->prefix_table . $table_etrangere])) {
                            $prefixe_table = $this->prefix_table;
                        }
                        else {
                            $prefixe_table = false;
                        }
                        $configs['prefix_table'] = $prefixe_table;
//                        echo '<br>E> ' . $table . ' : ' . $prefixe_table . $table_etrangere . '_id';
//
                        //exception de compatibilité
                        if(!isset($this->_bilan[$prefixe_table . $table_etrangere])) {
//                            echo '<br>-- ' . $prefixe_table . $table_etrangere;
                            //controle sur les clef etrangeres.
                            //reduction pour les jointures nommer $xxxxxx_xxxx_yyy_id
                            $table_explode = explode('_', $table_etrangere);
                            $table_reduc = false;
                            foreach($table_explode as $a => $partial) {
                                if(($a + 1) < count($table_explode)) {
                                    if($a > 0) {
                                        $table_reduc .= '_';
                                    }
                                    $table_reduc .= $partial;
                                }
                            }
//                            echo '<br>--$table_reduc--' . $table_reduc;
                            if(isset($this->_bilan[$table_reduc])) {
                                //Self-referencing
                                //clef sur la table elle même
                                if($table_reduc == $table) {
//                                    echo '<br>Self-referencing> ' . $table_reduc . '==' . $table_etrangere;
                                    $this->setDeclarationSelfReferencing($table, $champ_name, $table_etrangere, $configs);
//                                        $this->getLogger()->info('Jointure Self-referencing', array('table' => $table, 'champ' => $table_etrangere));
                                    continue;
                                }
                                //clef sur autre table
                                else {
//                                    echo '<br>ManyToOne +> ' . $table . ' : ' . $table_reduc . '==' . $table_etrangere;
                                    $configs['table_etrangere'] = $table_etrangere;
//                                        $this->getLogger()->info('Jointure avec nomenclature étandue (xxxx_xxxx_yyyy_id)', array('table' => $table, 'champ' => $champ_name, 'table_reduc' => $table_reduc));
                                    $this->setJoin_ManyToOne_OneToMany_Add($table, $champ_name, $table_etrangere, $table_reduc, $configs);
//                                    $this->setDeclarationClef($table, $champ_name, $configs);
                                    continue;
                                }
                            }
                            else {
//                                echo '<br><b>Exception clef étrangère sans correspondance-> ' . $table_reduc . ' / .' . $table . '</b>';
                                $this->bilan_errs['table_manquante'][$table_reduc] = true;
                                $this->getLogger()->err('Exception clef étrangère sans correspondance (mauvaise nomenclature))', array('table' => $table, 'champ' => $champ_name));
//                                throw new Exception('Exception clef étrangère sans correspondance (mauvaise nomenclature)) - ' . $table . ':' . $champ_name);
                            }
                        }
                        else {
//                            echo '<br>setJoin_ManyToOne_OneToMany -> ' . $table . '--' . $table_etrangere . '_id';
                            //exceptions connus
                            $this->setJoin_ManyToOne_OneToMany($table, $champ_name, $table_etrangere, $configs);
//                            $this->setDeclarationClef($table, $champ_name, $configs);
                            continue;
                        }
                    }
                    else {
                        $this->setJoin_Any($table, $champ_name, $configs);
                        continue;
                    }
//                    echo '<br>' . $infos['Field'] . '---' . $infos['Default'];
//                    echo "<option value='" . $table . "'>---------" . $infos['Field'] . " (" . $infos['Type'] . "/" . $infos['Null'] . "/" . $infos['Key'] . "/" . $infos['Default'] . "/" . $infos['Extra'] . ")</option>";
                }
                //ManyToMany
                else {
                    if($infos['Key'] == 'PRI' && $champ_name != 'id' && substr($champ_name, -3) == '_id') {
                        $table_etrangere = substr($champ_name, 0, -3);
                        //echo '<br>*******************************************' . $infos['Key'] . '--' . $table . '--' . $champ_name . '--' . $table_etrangere;
                        //                              'user_group', 'user_id', 'user'
                        $this->setJoin_ManyToMany($table, $champ_name, $configs);
                        continue;
                    }
                    else {
                        $this->getLogger()->err('Exception clef primaire mauvaise nomenclature ', array('table' => $table, 'champ' => $champ_name));
                    }

                    $this->getLogger()->info('Table ABSTRACT type ManyToMany', array('table' => $table));
                }
            }
        }

        $this->initializeDeclarationManyToMany();

        $this->setGene();

        foreach($this->_bilan as $table => $champ) {
            if($this->getGene()->hasChamps($table)) {
                foreach($this->getGene()->getChamps($table) as $champ => $configs) {
                    if(isset($this->_gene[$table]['join'][$champ])) {
                        throw new Exception('Doublon de getter et setter: un champ ne doit pas porter le nom d\'une clef étrangère: ' . $table . ':' . $champ);
//                        echo '<br>---join> ' . $table . '--' . $champ;
                    }
                    if(isset($this->_gene[$table]['join_reverse'][$champ])) {
                        $this->_gene[$table]['join_reverse'][$champ];
//                        echo '<br>---join_reverse> ' . $table . '--' . $champ;
                    }
                }
            }
        }

        if($is_assemblage) {
            $this->generateEntity();
            if($this->gene_config->hasSuperclass()) {
                $this->generateEntityBase();
            }
            $this->generateRepository();
        }
    }

    public function generateEntity($table_generate = false, $file_contents = false)
    {
        foreach($this->_bilan as $table => $value) {
            if($table == $table_generate || !$table_generate) {

                if($this->_clef_primaire[$table]) {
//                    $fichier = new Fichier($this->dir_root . str_replace('\\', '/', $this->entityNamespace_s) . '/' . $this->camelize($table) . '.php');
//                    $filename = $this->dir_root . str_replace('\\', '/', $this->entityNamespace_s) . '/' . $this->camelize($table) . '.php';
                    $filename = $this->gene_config->getSrcDirEntity() . '/' . $this->camelize($table) . '.php';
//                    $fichier->chProperties(0777);

                    $this->getDeclarationEntityStart($table);
//
                    $this->getDeclarationIds($table);
                    $this->getDeclarationManyToOnes($table);
                    $this->getDeclarationOneToManys($table);
                    $this->getDeclarationManyToOneAdds($table);
                    $this->getDeclarationOneToManyAdds($table);
                    $this->getDeclarationManyToManys($table);
//                    $this->getDeclarationJoinSelfReferencings($table);
//                    $this->getDeclarationJoinSelfReferencingReverses($table);
                    $this->getDeclarationTree($table);
//                    $this->getDeclarationSortable($table);
                    $this->getDeclarationNormals($table);
                    $this->getDeclarationJoinReverseContructs($table);
//
////                    $this->getSleep($table);
                    $this->getGetterIds($table);
                    $this->getGetterSetterJoins($table);
                    $this->getGetterSetterJoinReverses($table);
                    $this->getGetterSetterManyToOneAdds($table);
                    $this->getGetterSetterOneToManyAdds($table);
                    $this->getGetterSetterJoinManyToMany($table);
//                    $this->getGetterSetterJoinSelfReferencings($table);
//                    $this->getGetterSetterJoinSelfReferencingReverses($table);
                    $this->getGetterSetterTree($table);
//                    $this->getGetterSetterSortable($table);
                    $this->getGetterSetterNormals($table);
//
//
                    $this->getLifecycleCallbacks($table);
//                    $this->getSetterUpdatedAt($table);
//
                    $this->getLoadValidatorMetadata($table);
//
                    $this->getDeclarationEntityEnd();

//                    $fichier->put($this->_code_entity);
                    try {
//                        echo 'Table: ' . $table;
                        if($this->getIsFileContent()) {

//                            if(file_exists($filename) && $this->gene_config->hasSecurity() && $table == $this->underscore($this->gene_config->getSecurityEntity())) {
//                                continue;
//                            }
//                            else {
//                            echo('<br>' . $filename);
                            file_put_contents($filename, $this->_code_entity);
//                            }
                        }
                    }
                    catch(Exception $e) {
                        throw new Exception('le dossier Entity n existe pas ou le dossier ' . $filename . ' n a pas les droits: ' . $e->getMessage());
                    }
//                    echo '<br><br>EB:' . $table . ' <textarea>' . $this->_code_entity . '</textarea>';
//                    $fichier->chProperties(0777);
                    $this->code_entity[$table] = $this->_code_entity;
                }
            }
        }
    }

    public function generateEntityBase($table_generate = false)
    {
        foreach($this->_bilan as $table => $value) {
            if($table == $table_generate || !$table_generate) {

                if($this->_clef_primaire[$table]) {
//                    $fichier = new Fichier($this->dir_root . str_replace('\\', '/', $this->entityNamespace_s) . 'Base/' . $this->camelize($table) . 'Base.php');
//                    $filename = $this->dir_root . str_replace('\\', '/', $this->entityNamespace_m) . '/' . $this->camelize($table) . 'Base.php';
                    $filename = $this->gene_config->getSrcDirModel() . '/' . $this->camelize($table) . 'Base.php';
//                    echo '<br>E: ' . $filename;
                    if(file_exists($filename) === false) {
//                        $fichier->chProperties(0777);

                        $this->getDeclarationEntityBaseStart($table);
                        $this->getDeclarationEntityBaseEnd();

                        try {
                            if($this->getIsFileContent()) {
                                if(file_exists($filename)) {
                                    continue;
                                }
                                else {
                                    file_put_contents($filename, $this->_code_entity_base);
                                }
                            }
                        }
                        catch(Exception $e) {
                            throw new Exception('le dossier EntityBase n existe pas ou le dossier ' . $filename . ' n a pas les droits');
                        }

                        $this->code_entity_base[$table] = $this->_code_entity_base;
                    }
                }
            }
        }
    }

    public function generateRepository()
    {
        //code complément plus tard
        //use Doctrine\Extensions\NestedSet\Node;

        foreach($this->_bilan as $table => $value) {

            //si table user
            if($this->_clef_primaire[$table]) {
                $implements = $user_namespaces = $user_functions = false;
                if($this->gene_config->hasSecurity()) {
                    if($table == $this->underscore($this->gene_config->getSecurityEntity())) {
                        $implements = ' implements UserProviderInterface';
                        $user_namespaces = <<<eof
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

eof;
                        $user_functions = <<<eof
    public function loadUserByUsername(\$username)
    {
        \$user = \$this
            ->createQueryBuilder('u')
            ->select('u, g')
            ->leftJoin('u.groups', 'g')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', \$username)
            ->setParameter('email', \$username)
            ->getQuery()
            ->getSingleResult()
        ;

        return \$user;
    }


eof;
                    }
                }

                if($this->getGene()->hasTree($table)) {
                    $useRepositoryClass = 'use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
';
                    $repositoryClass = 'NestedTreeRepository';
                }
                else {
                    $useRepositoryClass = 'use Doctrine\ORM\EntityRepository;
';
                    $repositoryClass = 'EntityRepository';
                }

//                    $fichier = new Fichier($this->dir_root . str_replace('\\', '/', $this->repositoryNamespace_s) . '/' . $this->camelize($table) . 'Repository.php');
//                $filename = $this->dir_root . str_replace('\\', '/', $this->repositoryNamespace_s) . '/' . $this->camelize($table) . 'Repository.php';
                $filename = $this->gene_config->getSrcDirRepository() . '/' . $this->camelize($table) . 'Repository.php';
//                    echo '<br>R: ' . $filename;
                $this->_code_repository = <<<eof
<?php

namespace {$this->gene_config->getDirRepository()};

{$useRepositoryClass}{$user_namespaces}

class {$this->camelize($table)}Repository extends {$repositoryClass}{$implements} {

{$user_functions}
}

eof;
//$this->_code_repository = 'coucou';
//echo $this->_code_repository;

                if(file_exists($filename) === false) {
//                        $fichier->chProperties(0777);
                    try {
                        if($this->getIsFileContent()) {
                            file_put_contents($filename, $this->_code_repository);
                        }
                    }
                    catch(Exception $e) {
                        throw new Exception('le dossier Repository n existe pas ou le dossier ' . $filename . ' n a pas les droits: ' . $e->getMessage());
                    }
//                        $fichier->put(
//                        );
//                        $fichier->chProperties(0777);
//                    die('-'.$this->_code_repository);
                }
                $this->code_repository[$table] = $this->_code_repository;
            }
        }
    }

}

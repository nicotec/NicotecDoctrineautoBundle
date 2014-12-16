<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator\Entity;

use Exception;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\GeneConfig;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Register;

abstract class GetterGenerator extends FormatGenerator
{

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var GeneConfig
     */
    protected $gene_config;
    protected $container, $register;
    protected $_bilan, $_clef_primaire;
    private $doublonArrayCollections = [];

    public function __construct(ContainerInterface $container, Register $register)
    {
        $this->container = $container;
        $this->register = $register;
        $this->kernel = $container->get('kernel');
        $this->gene_config = new GeneConfig($container);

        $log = new Logger('EntityGenerator');
        $log->pushHandler(new StreamHandler($this->kernel->getLogDir() . '/EntityGenerator.log'));
        $this->log = $log;
    }

    public function getGeneConfig()
    {
        return $this->gene_config;
    }

    protected function getDeclarationEntityStart($table)
    {
        $use_ArrayCollection = $repositoryClass = $is_gedmo = $as_gedmo = $declare_tree = $declare_life = $userInterface = $use_user = $attribut_role = false;

        if($this->getGene()->hasJoinsReverses($table) || ($this->getGene()->hasJoinsManys($table) && !$this->getGene()->hasJoinsReverses($table)) || $this->getGene()->hasTree($table)) {
            $use_ArrayCollection = '
use Doctrine\Common\Collections\ArrayCollection;';
        }

        $repositoryClass = $this->gene_config->getDirRepository() . '\\' . $this->camelize($table) . 'Repository';
        if($this->getGene()->hasLifecycleCallbacks($table)) {
//            $is_gedmo = true;
            $declare_tree = '
 * @ORM\HasLifecycleCallbacks()';
        }
//        if($this->getGene()->hasSortable($table)) {
//            $is_gedmo = true;
//            $repositoryClass = 'Gedmo\Sortable\Entity\Repository\SortableRepository';
//        }
        if($this->getGene()->hasTree($table)) {
            $is_gedmo = true;
            $declare_tree = '
 * @Gedmo\Tree(type="nested")';
//            $repositoryClass = 'Gedmo\Tree\Entity\Repository\NestedTreeRepository';
        }

        //user
        if($this->gene_config->hasSecurity() && $this->underscore($this->gene_config->getSecurityEntity()) == $table) {
            if($table == $this->underscore($this->gene_config->getSecurityEntity())) {
                $userInterface = ' implements UserInterface';
                $use_user = '
use Symfony\Component\Security\Core\User\UserInterface;';
            }
//            $attribut_role = '    protected $roles;
//';
        }

        //user
        if($is_gedmo === true) {
            $as_gedmo = '
use Gedmo\Mapping\Annotation as Gedmo;';
        }

        $use_superclass = $extends_superclass = false;
        if($this->gene_config->hasSuperclass()) {
            $use_superclass = "
use {$this->gene_config->getDirModel()}\\{$this->camelize($table)}Base;";
            $extends_superclass = " extends {$this->camelize($table)}Base";
        }


        $this->_code_entity = <<<eof
<?php

namespace {$this->gene_config->getDirEntity()};

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping as ORM;{$as_gedmo}{$use_ArrayCollection}{$use_user}{$use_superclass}

/**
 * {$this->gene_config->getDirEntity()}\\{$this->camelize($table)}
 *
 * @{$this->prefix}Table(name="{$table}"){$declare_tree}{$declare_life}
 * @{$this->prefix}Entity(repositoryClass="{$repositoryClass}")
 */
class {$this->camelize($table)}{$extends_superclass}{$userInterface}
{

{$attribut_role}
eof
        ;
    }

    protected function getDeclarationEntityEnd()
    {
        $this->_code_entity .= <<<eof

}

eof
        ;
    }

    protected function getDeclarationEntityBaseStart($table)
    {
        $implement_user = $functions_user = false;

        if($this->getGene()->hasChamp($table, 'name')) {
            $to_string = 'name';
        }
        elseif($this->getGene()->hasChamp($table, 'nom')) {
            $to_string = 'nom';
        }
        elseif($this->getGene()->hasChamp($table, 'titre')) {
            $to_string = 'titre';
        }
        elseif($this->getGene()->hasChamp($table, 'title')) {
            $to_string = 'title';
        }
        elseif($this->getGene()->hasChamp($table, 'slug')) {
            $to_string = 'slug';
        }
        else {
            $to_string = 'id';
        }

        if($this->gene_config->hasSecurity() && $this->underscore($this->gene_config->getSecurityEntity()) == $table) {
            if($table == $this->underscore($this->gene_config->getSecurityEntity())) {
                $implement_user = ' implements AdvancedUserInterface';
                $functions_user = <<<eof
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return \$this->isActive;
    }
eof;
            }
        }

        $metadatas = false;
        if($this->getGene()->hasChamps($table)) {
            foreach($this->getGene()->getChamps($table) as $champ => $configs) {
                if($this->getGene()->isChampFile($champ)) {
                    //...
                }
                if($configs['infos']['null'] == 'true') {
                    $metadatas .= <<<eof
        //\$metadata->addPropertyConstraint('{$this->champPearMin($champ)}', new NotBlank());

eof
                    ;
                }
            }
        }

        $this->_code_entity_base = <<<eof
<?php

namespace {$this->gene_config->getDirModel()};

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\MappedSuperclass
 */
abstract class {$this->camelize($table)}Base{$implement_user}
{

    public static function loadValidatorMetadataBase(ClassMetadata \$metadata)
    {
{$metadatas}
    }

    public function getToString() {
        return \$this->{$to_string};
    }

{$functions_user}


eof
        ;
    }

    protected function getDeclarationEntityBaseEnd()
    {
        $this->_code_entity_base .= <<<eof

}

eof
        ;
    }

    protected function getDeclarationIds($table)
    {
        if($this->getGene()->hasId($table)) {
            $code = false;
//        echo '<br>' . $table;
            foreach($this->getGene()->getIds($table) as $champ => $configs) {

                $id_pear_min = $this->champPearMin($champ);
                $id_camelize = $this->camelize($champ);
                $code .= <<<eof
    /**
     * @var {$configs['infos']['type']} \${$this->champPearMin($champ)}
     *
     * @{$this->prefix}Column(name="{$champ}", type="{$configs['infos']['type']}", nullable=false)
     * @{$this->prefix}{$id_camelize}
     * @{$this->prefix}GeneratedValue(strategy="IDENTITY")
     */
    protected \${$id_pear_min};


eof
                ;
            }
            $this->_code_entity .= $code;
        }
        else {
            throw new Exception('Il manque quelques chose à la table ' . $table . ' (auto-increment, id dans la table non manytomany, ...)');
        }
    }

    protected function getDeclarationNormals($table)
    {
        if($this->getGene()->hasChamps($table)) {

            $code = false;
            foreach($this->getGene()->getChamps($table) as $champ => $configs) {
//                if($configs['infos']['null'] == 'false' && isset($configs['infos']['default']) && strlen($configs['infos']['default']) > 0) {
//                    $val_default = ' = "' . $configs['infos']['default'] . '"';
//                }
//                else {
                $val_default = false;
//                }

                $format_type = $this->getFormatType($configs['infos']);
                $code .= <<<eof
    /**
     * @var {$configs['infos']['type']} \${$this->champPearMin($champ)}
     *
     * @{$this->prefix}Column(name="{$champ}"{$format_type})
     */
    protected \${$this->champPearMin($champ)}{$val_default};


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getDeclarationManyToOnes($table)
    {
        //, cascade={"persist"}
        if($this->getGene()->hasJoins($table)) {
            $code = '';
            foreach($this->getGene()->getJoins($table) as $champ => $configs) {
                $format_type = $this->getFormatType($configs['infos']);

                $code .= <<<eof
    /**
     * @{$this->prefix}ManyToOne(targetEntity="{$this->camelize($configs['prefix_table'] . $configs['table_etrangere'])}", inversedBy="{$this->champPearMin($table)}s")
     * @{$this->prefix}JoinColumn(name="{$champ}_id", referencedColumnName="id", nullable=true)
     */
    protected \${$this->champPearMin($champ)};

    /**
     * @var {$configs['infos']['type']} \${$this->champPearMin($champ)}Id
     *
     * @{$this->prefix}Column(name="{$champ}_id"{$format_type})
     */
    protected \${$this->champPearMin($champ)}Id;


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getDeclarationOneToManys($table)
    {
        if($this->getGene()->hasJoinsReverses($table)) {
            $code = false;
            foreach($this->getGene()->getJoinsReverses($table) as $champ => $configs) {
                $code .= <<<eof
    /**
     * @{$this->prefix}OneToMany(targetEntity="{$this->camelize($champ)}", mappedBy="{$this->champPearMin($table)}", cascade={"remove"})
     */
    protected \${$this->champPearMin($champ)}s;


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getDeclarationManyToOneAdds($table)
    {
        if($this->getGene()->hasJoinsAdd($table)) {
            $code = '';
            foreach($this->getGene()->getJoinsAdd($table) as $champ => $configs) {
                echo '<br>' . $table;
                $format_type = $this->getFormatType($configs['infos']);
                $code .= <<<eof
    /**
     * @{$this->prefix}ManyToOne(targetEntity="{$this->camelize($configs['prefix_table'] . '_' . $configs['table_reduc'])}", inversedBy="{$this->champPearMin($champ . '_' . $table)}s")
     * @{$this->prefix}JoinColumn(name="{$champ}_id", referencedColumnName="id")
     */
    protected \${$this->champPearMin($champ)};

    /**
     * @var {$configs['infos']['type']} \${$this->champPearMin($champ)}Id
     *
     * @{$this->prefix}Column(name="{$champ}_id"{$format_type})
     */
    protected \${$this->champPearMin($champ)}Id;


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getDeclarationOneToManyAdds($table)
    {
        if($this->getGene()->hasJoinsAddReverses($table)) {
            $code = false;
            foreach($this->getGene()->getJoinsAddReverses($table) as $champ => $configs) {
//                var_dump($configs);die;
                echo '<br>----' . __METHOD__ . '--' . $table;
                $code .= <<<eof
    /**
     * @{$this->prefix}OneToMany(targetEntity="{$this->camelize($configs['table'])}", mappedBy="{$this->champPearMin($configs['table_etrangere'])}", cascade={"remove"})
     */
    protected \${$this->champPearMin($configs['table_etrangere'] . '_' . $configs['table'])}s;


eof
                ;
//            echo '<br>getDeclarationJoinAddReverses> '.$table.'--'.$champ;
            }
            $this->_code_entity .= '
' . $code;
        }
    }

    protected function getDeclarationManyToManys($table)
    {
//        echo '<br>--' . $table . '--' . $this->getGene()->hasJoins($table);
//        die;
        if($this->getGene()->hasJoinsManys($table)) {
            $code = false;
            foreach($this->getGene()->getJoinsManys($table) as $table_liaison => $configs) {
                if($this->getGene()->hasJoins($configs['table_etrangere'])) {
                    continue;
                }
//            echo '<br>'.$table.'--'.
                //echo '<br>+++++++++++++++++++++++++++++++<b>' . $table . '</b>--' . $configs['num'] . '--' . $configs['table_etrangere'];
                if($configs['num'] == '1') {
                    $code .= <<<eof
    /**
     * @{$this->prefix}ManyToMany(targetEntity="{$this->camelize($configs['table_etrangere'])}", inversedBy="{$this->champPearMin($table)}s")
     * @{$this->prefix}JoinTable(name="{$table_liaison}")
     */
    protected \${$this->champPearMin($configs['table_etrangere'])}s;


eof
                    ;
                }
                if($configs['num'] == '2') {
                    //echo '**'.$this->camelize($configs['table_etrangere']).'--'.$this->champPearMin($table);
                    //die;
                    $code .= <<<eof
    /**
     * @{$this->prefix}ManyToMany(targetEntity="{$this->camelize($configs['table_etrangere'])}", mappedBy="{$this->champPearMin($table)}s")
     */
    protected \${$this->champPearMin($configs['table_etrangere'])}s;


eof
                    ;
                }
            }

            $this->_code_entity .= '
' . $code;
            //echo '<br>' . $code . '<br><br><br>';
            //die;
        }
    }

    protected function getDeclarationJoinSelfReferencings($table)
    {
        if($this->getGene()->hasJoinSelfReferencings($table)) {
            $code = false;
            foreach($this->getGene()->getJoinSelfReferencings($table) as $champ => $configs) {
                $code .= <<<eof
    /**
     * @{$this->prefix}ManyToOne(targetEntity="{$this->camelize($table)}", inversedBy="{$this->champPearMin($configs['champ_children'])}s")
     * @{$this->prefix}JoinColumn(name="{$champ}_id", referencedColumnName="id")
     */
    protected \${$this->champPearMin($champ)};


eof
                ;
            }
            $this->_code_entity .= '
' . $code;
        }
    }

    protected function getDeclarationJoinSelfReferencingReverses($table)
    {
        if($this->getGene()->hasJoinSelfReferencingReverses($table)) {
            $code = false;
            foreach($this->getGene()->getJoinSelfReferencingReverses($table) as $champ => $configs) {
                $code .= <<<eof
    /**
     * @{$this->prefix}OneToMany(targetEntity="{$this->camelize($table)}", mappedBy="{$this->champPearMin($champ)}", cascade={"remove"})
     */
    protected \${$this->champPearMin($champ)}s;


eof
                ;
            }
            $this->_code_entity .= '
' . $code;
        }
    }

    protected function getDeclarationJoinReverseContructs($table)
    {
        $is_join_reverse = $this->getGene()->hasJoinsReverses($table);
        $is_join_add_reverse = $this->getGene()->hasJoinsAddReverses($table);
        $is_join_reverse_sr = $this->getGene()->hasJoinSelfReferencingReverses($table);
        $is_join_reverse_many = $this->getGene()->hasJoinsReverseManys($table);
        $is_tree = $this->getGene()->hasTree($table);
        $is_salt_user = false;

        //user
        if($this->gene_config->hasSecurity() && $this->underscore($this->gene_config->getSecurityEntity()) == $table) {
            if($table == $this->underscore($this->gene_config->getSecurityEntity())) {
                $is_salt_user = true;
            }
        }

        //calcul des default
        $default = false;
//        echo 'coucou';die;
        if($this->getGene()->hasChamps($table)) {
            foreach($this->getGene()->getChamps($table) as $champ => $configs) {
//            print_r($field);
//            echo '<br><br>$field: '.$field['default'];
//            die;
                if($configs['infos']['default'] !== false) {
                    if($this->getGene()->getChampType($table, $champ) == 'boolean') {
                        if($configs['infos']['default']) {
                            $bool = 'true';
                        }
                        else {
                            $bool = 'false';
                        }
                        $default .= <<<eof
        \$this->{$this->champPearMin($champ)} = {$bool};

eof
                        ;
                    }
                    else {
//                $default = $field['infos']['default'];
                        $default .= <<<eof
        \$this->{$this->champPearMin($champ)} = "{$configs['infos']['default']}";

eof
                        ;
                    }
                }
            }
        }


        if($default || $is_salt_user || $is_join_reverse || $is_join_add_reverse || $is_join_reverse_sr || $is_join_reverse_many || $is_tree) {
            //start
//            $code = '    public function __construct(EntityManager $em)
//    {
//        \$this->_em = $em;
//';
            $code = '    public function __construct()
    {
';

            if($default) {
                $code .= $default;
            }

            //join_reverse
            if($is_join_reverse) {
                foreach($this->getGene()->getJoinsReverses($table) as $champ => $configs) {
                    $cpm = $this->champPearMin($champ);
//                    echo '<br>--' . $table . '*' . $cpm;
                    if(isset($this->doublonArrayCollections[$table . '*' . $cpm])) {
                        continue;
                    }
                    $code .= <<<eof
        \$this->{$cpm}s = new ArrayCollection();

eof
                    ;
                    $this->doublonArrayCollections[$table . '*' . $cpm] = true;
                }
            }

            //join_reverse_add
            if($is_join_add_reverse) {
                foreach($this->getGene()->getJoinsAddReverses($table) as $champ => $configs) {
                    $cpm = $this->champPearMin($champ);
                    if(isset($this->doublonArrayCollections[$table . '*' . $cpm])) {
                        continue;
                    }
                    $code .= <<<eof
        \$this->{$this->champPearMin($configs['table_etrangere'].'_'.$configs['table'])}s = new ArrayCollection();

eof
                    ;
                    $this->doublonArrayCollections[$table . '*' . $cpm] = true;
                }
            }

            //join_reverse_sr
            if($is_join_reverse_sr) {
                foreach($this->getGene()->getJoinSelfReferencingReverses($table) as $champ => $configs) {
                    $cpm = $this->champPearMin($champ);
                    if(isset($this->doublonArrayCollections[$table . '*' . $cpm])) {
                        continue;
                    }
                    $code .= <<<eof
        \$this->{$this->champPearMin($champ)}Children = new ArrayCollection();

eof
                    ;
                    $this->doublonArrayCollections[$table . '*' . $cpm] = true;
                }
            }

            //join_reverse_many
            if($is_join_reverse_many) {
                foreach($this->getGene()->getJoinsReverseManys($table) as $champ => $configs) {
//                    echo '<br><b>join_reverse_many</b> ' . $table . ' - ' . $champ;
                    $cpm = $this->champPearMin($champ);
                    if(isset($this->doublonArrayCollections[$table . '*' . $cpm])) {
                        continue;
                    }
                    $code .= <<<eof
        \$this->{$this->champPearMin($champ)}s = new ArrayCollection();

eof
                    ;
                    $this->doublonArrayCollections[$table . '*' . $cpm] = true;
                }
            }

            //tree
            if($is_tree) {
                $code .= <<<eof
        \$this->children = new ArrayCollection();

eof
                ;
            }

            //user
            if($is_salt_user) {
                $code .= <<<eof
        \$this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

eof
                ;
            }

            $code .= <<<eof
    }


eof
            ;

            $this->_code_entity .= $code;
        }
    }

//    protected function getSleep($table)
//    {
//        if($table == 'user') {
//            $code = <<<eof
//    public function __sleep()
//    {
//        return array('id', 'username', 'password', 'salt'); // add your own fields
//    }
//
//
//eof
//            ;
//            $this->_code_entity .= $code;
//        }
//    }
    protected function getGetterIds($table)
    {
        if($this->getGene()->hasId($table)) {
            $code = false;

            foreach($this->getGene()->getIds($table) as $champ => $configs) {
                $id_pear_min = $this->champPearMin($champ);
                $id_camelize = $this->camelize($champ);
                $code .= <<<eof
    /**
     * Get \${$this->champPearMin($champ)}
     *
     * @return {$configs['infos']['type']}
     */
    public function get{$this->camelize($champ)}()
    {
        return \$this->{$champ};
    }

    public function set{$id_camelize}(\${$id_pear_min})
    {
        \$this->{$id_pear_min} = \${$id_pear_min};
    }


eof
                ;
//    /**
//     * Set \${$this->champPearMin($champ)}
//     *
//     * @param {$configs['infos']['type']} \${$this->champPearMin($champ)}
//     */
//    public function set{$this->camelize($champ)}(\${$champ})
//    {
//        \$this->{$champ} = \${$champ};
//    }
//
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getGetterSetterJoinReverses($table)
    {
        if($this->getGene()->hasJoinsReverses($table)) {
            $code = false;
            foreach($this->getGene()->getJoinsReverses($table) as $champ => $configs) {
//                echo '<br><b>join_reverse</b> ' . $table . ' - ' . $champ;
                $code .= <<<eof
    public function get{$this->camelize($champ)}s()
    {
        return \$this->{$this->champPearMin($champ)}s;
    }


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getGetterSetterJoinManyToMany($table)
    {
        if($this->getGene()->hasJoinsReverseManys($table)) {
            $code = false;
            foreach($this->getGene()->getJoinsReverseManys($table) as $champ => $configs) {
//                echo '<br>' . $table . ' --' . $champ;
                //empêche doublon d'un OneToMany + ManyToMany sur la même table: user -> messages -> user
                //
//                icorrecte $table dans ->hasJoinsReverses($table)
//                if($this->getGene()->hasJoinsManys($table) && $this->getGene()->hasJoinsReverses($table)) {
//                    continue;
//                }
//                echo '<br><b>getGetterSetterJoinManyToMany - join_reverse_many:</b> ' . $table . ' - ' . $champ;
//                echo $this->champPearMin($champ);
                if($this->champPearMin($champ) != 'role') {
                    $code .= <<<eof
    public function get{$this->camelize($champ)}s()
    {
        return \$this->{$this->champPearMin($champ)}s;
    }


eof
                    ;
                }
                $code .= <<<eof
    public function add{$this->camelize($champ)}(\${$this->champPearMin($champ)})
    {
        \$this->{$this->champPearMin($champ)}s[] = \${$this->champPearMin($champ)};
    }


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getGetterSetterJoinSelfReferencings($table)
    {
        if($this->getGene()->hasJoinSelfReferencings($table)) {
            $code = false;
            foreach($this->getGene()->getJoinSelfReferencings($table) as $champ => $configs) {
                $code .= <<<eof
    public function get{$this->camelize($champ)}()
    {
        return \$this->{$this->champPearMin($champ)};
    }

    public function set{$this->camelize($champ)}(\${$champ})
    {
        \$this->{$this->champPearMin($champ)} = \${$champ};
    }


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getGetterSetterJoinSelfReferencingReverses($table)
    {
        if($this->getGene()->hasJoinSelfReferencingReverses($table)) {
            $code = false;
            foreach($this->getGene()->getJoinSelfReferencingReverses($table) as $champ => $configs) {
                $code .= <<<eof
    public function get{$this->camelize($champ)}s()
    {
        return \$this->{$this->champPearMin($champ)}s;
    }


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getGetterSetterJoins($table)
    {
        if($this->getGene()->hasJoins($table)) {
            $code = '';
            foreach($this->getGene()->getJoins($table) as $champ => $configs) {

                $champ_pear_min = $this->champPearMin($champ);
                $champ_camelize = $this->camelize($champ);
                $table_etrangere = $this->camelize($configs['table_etrangere']);

                $code .= <<<eof
    public function get{$champ_camelize}Id()
    {
        return \$this->{$champ_pear_min}Id;
    }

    public function get{$champ_camelize}()
    {
        return \$this->{$champ_pear_min};
    }

    public function set{$champ_camelize}(\${$champ})
    {
        \$this->{$champ_pear_min} = \${$champ};
    }


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getGetterSetterManyToOneAdds($table)
    {
        if($this->getGene()->hasJoinsAdd($table)) {
            $code = false;
            foreach($this->getGene()->getJoinsAdd($table) as $champ => $configs) {
                $champ_pear_min = $this->champPearMin($champ);
                $champ_camelize = $this->camelize($champ);
                $table_etrangere = $this->camelize($configs['table_reduc']);

                echo '<br>----' . __FUNCTION__ . '--' . $table . '--' . $champ.'--'.$table_etrangere;
                $code .= <<<eof
    public function get{$champ_camelize}Id()
    {
        return \$this->{$champ_pear_min}Id;
    }

    public function get{$champ_camelize}()
    {
        return \$this->{$champ_pear_min};
    }

    public function set{$champ_camelize}({$table_etrangere} \${$champ})
    {
        \$this->{$champ_pear_min} = \${$champ};
    }


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getGetterSetterOneToManyAdds($table)
    {
        if($this->getGene()->hasJoinsAddReverses($table)) {
            $code = false;
            foreach($this->getGene()->getJoinsAddReverses($table) as $champ => $configs) {
//                echo '<br><b>join_reverse</b> ' . $table . ' - ' . $champ;
                $code .= <<<eof
    public function get{$this->camelize($configs['table_etrangere'] . '_' . $configs['table'])}s()
    {
        return \$this->{$this->champPearMin($configs['table_etrangere'])}{$this->camelize($configs['table'])}s;
    }


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getGetterSetterNormals($table)
    {
        if($this->getGene()->hasChamps($table)) {

            $code = false;
            foreach($this->getGene()->getChamps($table) as $champ => $configs) {

//                $format_type = $this->getFormatType($configs['infos']);

                $champ_pear_min = $this->champPearMin($champ);
                $champ_camelize = $this->camelize($champ);

                $class = false;
//                if($configs['infos']['type'] == 'datetime') {
//                    $class = '\Datetime ';
//                }

                $code .= <<<eof
    /**
     * Get {$champ_pear_min}
     *
     * @return {$configs['infos']['type']}
     */
    public function get{$champ_camelize}()
    {
        return \$this->{$champ_pear_min};
    }


eof
                ;
//                //exception setter
//                //si sortable
//                if(in_array($champ, $this->sortable)) {
//                    continue;
//                }

                $code .= <<<eof
    /**
     * Set {$champ_pear_min}
     *
     * @param {$configs['infos']['type']} {$champ_pear_min}
     */
    public function set{$champ_camelize}({$class}\${$champ})
    {
        \$this->{$champ_pear_min} = \${$champ};
    }


eof
                ;
            }

            //user
            if($this->gene_config->hasSecurity() && $this->underscore($this->gene_config->getSecurityEntity()) == $table) {
                if($table == $this->underscore($this->gene_config->getSecurityEntity())) {
                    $code .= <<<eof
    public function getRoles(\$isObject = false)
    {
        if(!\$isObject){
            \$aRoles = [];
            foreach(\$this->roles as \$role) {
                \$aRoles[] = \$role->getRole();
            }

            return \$aRoles;
        }

        return \$this->roles;
    }

    public function unsetRole(\$role)
    {
        \$keys = array_keys(\$this->roles, \$role);
        if(count(\$keys) > 0){
            unset(\$this->roles[\$keys[0]]);
        }
    }

    public function isEqualTo(UserInterface \$user)
    {
        return \$this->username === \$user->getUsername();
    }

    public function eraseCredentials()
    {
    }

eof;
                }
            }


            $this->_code_entity .= $code;
        }
    }

    protected function getGetterSetterSortable($table)
    {
        if($this->getGene()->hasSortable($table)) {


            $code = '';
            if($this->getGene()->hasSortablePosition($table)) {

                $code .= <<<eof
    public function setPosition(\$position)
    {
        \$this->position = \$position;
    }

    public function getPosition()
    {
        return \$this->position;
    }


eof
                ;
            }
            if($this->getGene()->hasSortableCategory($table)) {

                $code .= <<<eof
    public function setCategory(\$category)
    {
        \$this->category = \$category;
    }

    public function getCategory()
    {
        return \$this->category;
    }


eof
                ;
            }
            $this->_code_entity .= $code;
        }
    }

    protected function getFormatType($infos)
    {
        $format_type = '';
        foreach($infos as $key => $value) {
            if($key == 'null') {
                $format_type .= ', nullable = ' . $value . '';
            }
            elseif($key == 'default') {
                $format_type .= '';
            }
            else {
                $format_type .= ', ' . $key . ' = "' . $value . '"';
            }
        }
        return $format_type;
    }

    protected function getLifecycleCallbacks($table)
    {
        if($this->getGene()->hasLifecycleCallbacks($table)) {
//        if(isset($this->_gene[$table]['created_at']) && isset($this->_gene[$table]['updated_at'])) {
            $this->_code_entity .= <<<eof
    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if(!\$this->id) {
            \$this->set{$this->camelize($this->getGene()->getCreated($table))}(new \DateTime());
        }

        \$this->set{$this->camelize($this->getGene()->getUpdated($table))}(new \DateTime());
    }


eof
            ;
        }
    }

//    protected function getSetterUpdatedAt($table)
//    {
//        if(isset($this->_gene[$table]['updated_at'])) {
//            $this->_code_entity .= <<<eof
//    /**
//     * @ORM\PostPersist()
//     */
//    public function postPersist()
//    {
//        \$this->updatedAt = new \DateTime();
//    }
//
//
//eof
//            ;
//        }
//    }

    protected function getDeclarationTree($table)
    {
        if($this->getGene()->hasTree($table)) {

            $trees = $this->getGene()->getTrees($table);
            $root2 = $this->champPearMin($trees['root']);

            $this->_code_entity .= <<<eof
    /**
     * @Gedmo\TreeLeft
     * @{$this->prefix}Column(name="{$trees['left']}", type="integer")
     */
    protected \${$trees['left']};

    /**
     * @Gedmo\TreeRight
     * @{$this->prefix}Column(name="{$trees['right']}", type="integer")
     */
    protected \${$trees['right']};

    /**
     * @Gedmo\TreeLevel
     * @{$this->prefix}Column(name="{$trees['level']}", type="integer")
     */
    protected \${$trees['level']};

    /**
     * @Gedmo\TreeRoot
     * @{$this->prefix}Column(name="{$trees['root']}", type="integer", nullable=true)
     */
    protected \${$root2};

    /**
     * @Gedmo\TreeParent
     * @{$this->prefix}ManyToOne(targetEntity="{$this->camelize($table)}", inversedBy="children")
     * @{$this->prefix}JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected \$parent;

    /**
     * @{$this->prefix}OneToMany(targetEntity="{$this->camelize($table)}", mappedBy="parent", cascade={"remove"})
     * @{$this->prefix}OrderBy({"{$trees['left']}" = "ASC"})
     */
    protected \$children;

    /**
     * @var integer \$parentId
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    protected \$parentId;

eof
            ;
        }
    }

    protected function getGetterSetterTree($table)
    {
        if($this->getGene()->hasTree($table)) {
            $trees = $this->getGene()->getTrees($table);
            $root = $this->camelize($trees['root']);
            $root2 = $this->champPearMin($trees['root']);
            $left = ucfirst($trees['left']);
            $right = ucfirst($trees['right']);
            $level = ucfirst($trees['level']);
            $this->_code_entity .= <<<eof
    /**
     * Get rootId
     *
     * @return integer
     */
    public function get{$root}()
    {
        return \$this->{$root2};
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function get{$left}()
    {
        return \$this->{$trees['left']};
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function get{$right}()
    {
        return \$this->{$trees['right']};
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function get{$level}()
    {
        return \$this->{$trees['level']};
    }

    public function setParent({$this->camelize($table)} \$parent = null)
    {
        \$this->parent = \$parent;
    }

    public function getParent()
    {
        return \$this->parent;
    }

    public function getParentId()
    {
        return \$this->parentId;
    }

    public function getChildren()
    {
        return \$this->children;
    }


eof
            ;
        }
    }

    protected function getDeclarationSortable($table)
    {
        if($this->getGene()->hasSortable($table)) {
            if($this->getGene()->hasSortablePosition($table)) {
                $this->_code_entity .= <<<eof
    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private \$position;


eof
                ;
            }

            if($this->getGene()->hasSortableCategory($table)) {
                $this->_code_entity .= <<<eof
    /**
     * @Gedmo\SortableGroup
     * @ORM\Column(name="category", type="string", length=128)
     */
    private \$category;


eof
                ;
            }
        }
    }

    protected function getLoadValidatorMetadata()
    {
        $this->_code_entity .= <<<eof
    public static function loadValidatorMetadata(ClassMetadata \$metadata)
    {
        if(method_exists(__CLASS__, __FUNCTION__ . 'Base')) {
            self::loadValidatorMetadataBase(\$metadata);
        }
    }


eof
        ;
    }

}

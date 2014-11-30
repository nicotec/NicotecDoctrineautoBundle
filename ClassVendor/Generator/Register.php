<?php

namespace Nicotec\DoctrineautoBundle\ClassVendor\Generator;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PDO;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

class Register {

    /**
     * @var Kernel
     */
    protected $kernel;
    protected $base, $pdo, $_bilan, $_clef_primaire = array();
    protected $container, $log, $session;
    protected $gene_config;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->kernel = $container->get('kernel');

        $log = new Logger('DoctrineGenerator');
        $log->pushHandler(new StreamHandler($this->kernel->getLogDir() . '/DoctrineGenerator.log'));
        $this->log = $log;
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * ex: $this->getLogger()->notice('exception sf_guard_ OK', array('table' => $table_etrangere, 'champ' => $champ));
     * @return Logger
     */
    public function getLogger()
    {
        return $this->log;
        //return $this->log;
    }

    /**
     * @return Controller
     */
    public function getBase()
    {
        return $this->base;
    }

    public function getBilan()
    {
        if(!$this->_bilan) {
            $this->setShowTable();
        }

        return $this->_bilan;
    }

    public function getClefPrimaire()
    {
        if(!$this->_clef_primaire) {
            $this->setShowTable();
        }

        return $this->_clef_primaire;
    }

    /**
     * @return GeneConfig
     */
    public function getGeneConfig()
    {
        if(!$this->gene_config) {
            $this->gene_config = new GeneConfig($this->container);
        }

        return $this->gene_config;
    }

    public function setConnectBySession()
    {
        $gene_config = $this->getGeneConfig();
//        echo $gene_config->getDatebaseName() . '--' . $gene_config->getDatebaseHost() . '--' . $gene_config->getDatebaseUser() . '--' . $gene_config->getDatebasePassword();
//        die;

        $this->setConnect($gene_config->getDatebaseName(), $gene_config->getDatebaseHost(), $gene_config->getDatebaseUser(), $gene_config->getDatebasePassword());
    }

    /**
     * @return Pdo
     */
    private function setConnect($base, $host, $user, $password)
    {
        if(!$this->pdo) {
            $this->base = $base;
            try {
                $this->pdo = new Pdo('mysql:dbname=' . $base . ';host=' . $host, $user, $password);
            }
            catch(PDOException $e) {
                throw new \Exception('Connexion échouée : ' . $e->getMessage());
            }
        }

        return $this->pdo;
    }

    public function setShowTable()
    {
        $this->_bilan = array();

        $result = $this->getPdo()->query("SHOW tables");
        foreach($result->fetchAll(PDO::FETCH_ASSOC) as $ligne) {
            $table = $ligne['Tables_in_' . $this->base];

            $trie = array_search($table, $this->getGeneConfig()->getTableIgnores());
            if($trie === 0 || $trie == true) {
                continue;
            }

            $sql_columns = $this->getPdo()->query("SHOW COLUMNS FROM " . $table);

            if($sql_columns) {
                foreach($sql_columns->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $this->_bilan[$table][$row['Field']]['Field'] = $row['Field'];
                    $this->_bilan[$table][$row['Field']]['Type'] = $row['Type'];
                    $this->_bilan[$table][$row['Field']]['Null'] = $row['Null'];
                    $this->_bilan[$table][$row['Field']]['Key'] = $row['Key'];
                    $this->_bilan[$table][$row['Field']]['Default'] = $row['Default']; //$row['Default']; NON GERER POUR LE MOMENT
                    $this->_bilan[$table][$row['Field']]['Extra'] = $row['Extra'];

                    if($row['Field'] != 'id' && $row['Key'] == 'PRI') {
                        if($row['Extra'] == 'auto_increment') {
                            $this->getLogger()->err('Erreur la clef primaire -auto_increment- doit être de nom -id-', $table . '-' . $row['Field'] . '-' . $row['Type']);
                        }
                    }
                }
                if(isset($this->_bilan[$table]['id']['Field'])) {
                    $this->_clef_primaire[$table] = true;
                }
                else {
                    $this->_clef_primaire[$table] = false;
                }
            }
            else {
                $this->getLogger()->err('Erreur départ');
            }
        }
    }

}

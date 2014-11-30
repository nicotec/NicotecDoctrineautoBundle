Generateur entities pour doctrine2
========================
Génère les entité des bases de données de doctrine2

1) Installation
----------------------------------

A) Ajouter à app/AppKernel.php (en dev uniquement):

if (in_array($this->getEnvironment(), array('dev', 'test'))) {
    $bundles[] = new Nicotec\DoctrineautoBundle\WsGeneEditBundle();
}



B) Ajouter à app/config/routing_dev.php

WsGeneEditBundle:
    resource: "@WsGeneEditBundle/Controller/"
    type:     annotation
    prefix:   /generator

_main:
    resource: routing.yml


C) Ajouter à app/autoload.php

$loader->add('WsGene', __DIR__ . '/../vendor/ws-generator/ws-generator');


http://.../app_dev.php/generator





2) Utilisation
----------------------------------

1) Aller à l'adresse url

http://.../app_dev.php/generator
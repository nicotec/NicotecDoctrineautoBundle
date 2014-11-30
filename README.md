# NicotecDoctrineautoBundle

Generateur des entités pour doctrine2
Génère les entités des bases de données de doctrine2

Installation
------------

1) Use [Composer](https://getcomposer.org/) to download the library

```
php composer.phar require nicotec/doctrineauto-bundle
```

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Nicotec\DoctrineautoBundle\NicotecDoctrineautoBundle();
        }
        // ...
    );
}
```

```php
// app/config/routing_dev.yml
nicotec_doctrineauto:
    resource: "@NicotecDoctrineautoBundle/Controller/"
    type:     annotation
    prefix:   /doctrineauto
```



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
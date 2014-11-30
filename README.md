# NicotecDoctrineautoBundle

Generateur automatique des entités à partir de mysql pour doctrine2

Prise en charge:
  * GedmoDoctrineExtension > stof.
  * User and Role.


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


Utilisation
------------

http://.../app_dev.php/doctrineauto
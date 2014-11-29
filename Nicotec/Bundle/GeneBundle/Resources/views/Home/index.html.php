<?php $view->extend('WsGeneEditBundle::layout_home.html.php') ?>


<?php $view['slots']->start('body') ?>
<b>Bienvenue dans le générateur automatique</b>

<br>
<br>
<div>GENERATOR:</div>
<div>
    <ul>
        <li><a href="<?php echo $view['router']->generate('ws_gene_edit_generator_index') ?>">Générer "entity" et "repository"</a></li>
    </ul>
</div>
<?php $view['slots']->stop() ?>

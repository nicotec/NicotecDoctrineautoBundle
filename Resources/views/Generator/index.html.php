<?php $view->extend('NicotecDoctrineautoBundle::layout_generator.html.php') ?>


<?php $view['slots']->start('body') ?>
<script type="text/javascript">
    $(function(){

        $('.ws_entity').click(function(){
            var t = $(this);
            $('.ws_entity_vu').slideUp(800, function(){
                $('#'+t.attr('id')+'_vu').slideDown()
            })
        })

    })
</script>

<div>Génération automatique</div>
<br>

<?php foreach($bases as $base): ?>

    <?php foreach($base->getCodeEntity() as $table => $entity): ?>
        <div class="ws_entity" id="<?php echo $base->getName() ?>_<?php echo $table ?>" style="background: #f1f1f1;padding: 5px;cursor: pointer"><b><?php echo $base->getName() ?></b> <?php echo $table ?></div>
        <div class="ws_entity_vu" id="<?php echo $base->getName() ?>_<?php echo $table ?>_vu" style="display: none"><textarea style="width: 800px;height: 800px;">{{ <?php echo $entity ?> }}</textarea></div>
        <br>
    <?php endforeach ?>
    <?php foreach($base->getCodeEntity() as $table => $entity): ?>
        '<?php echo $table ?>',
    <?php endforeach ?>
    <br>
    <br>
<?php endforeach ?>

<?php $view['slots']->stop() ?>


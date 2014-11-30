<html>
    <head>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

        <script type="text/javascript">
            $(function(){

//                $('#ws_test').click(function(){
//                    $.ajax ({
//                        type: 'POST',
//                        url : 'http://symfony2.webstation.fr/app_dev.php/crypt/1000',
//                        //            data: 'ws='+parameters,
//                        dataType: 'json',
//                        async: false,
//                        cache: false,
//                        success: function(request){
//                            request_ajax = debug_var = request;
//                            if(typeof callback_success != 'undefined')
//                                callback_success()
//                            //html_ajax = debug_var.html;
//                            //debug_var['html'] = '';
//                            //var p = new Print_r(debug_var);
//                            //$('#debug_hide').prepend('<br>'+p.get()+'');
//                            alert('ok')
//                            alert(request_ajax.site_web_id)
//                        },
//                        error: function(){
//                            //                    alert('error')
//                            //request_ajax.html = 'erreur'
//                            if(typeof callback_error != 'undefined')
//                                callback_error()
//                        }
//                    })
//                })

            })
        </script>
    </head>

    <body>

<!--        <div id="ws_test">test</div>-->

        <?php //$this->get('doctrine')->getRepositories("WsEditBundle:SiteWeb")      ?>
        <?php /*foreach($site_web_domaines as $site_web_domaine): ?>
            <br>n:<?php $site_web_domaine->getNom() ?>
            <?php if($site_web_domaine->getSiteWebId()): ?>
                <?php $site_web = $site_web_domaine->setEntityManager($em)->getTest($site_web_domaine->getSiteWebId()) ?>
                - <?php echo $site_web_domaine->getSiteWeb()->getNom() ?>
                - <?php echo $site_web_domaine->getDomaineExtension() ?>
                - <?php echo $site_web_domaine->setEntityManager($em)->getTest()->getNom() ?>
                - <?php //echo $site_web->getFamille()->findBy(array(1))      ?>
                - <?php //$site_web->getNom()      ?>
            <?php endif ?>
        <?php endforeach*/ ?>


        <?php //dfgdfg ?>

    </body>
</html>


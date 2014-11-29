<?php

namespace WsGene\EditBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use WsGene\EditBundle\Controller\ReController as Controller;

class AccueilController extends Controller {

    /**
     * @Route("/", name="gene.index")
     * @Template()
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('gene.setup'));
    }

}

<?php

namespace Nicotec\DoctrineautoBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Nicotec\DoctrineautoBundle\Controller\ReController as Controller;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @Route("/")
 */
class HomeController extends Controller {

    /**
     * @route("/", name="gene.index")
     * @Template()
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('gene.setup'));
    }

}


<?php

namespace Nicotec\DoctrineautoBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Entity\EntityGenerator;
use Nicotec\DoctrineautoBundle\ClassVendor\Generator\Register;

class ReController extends Controller {

    protected function getRegister()
    {
        $register = new Register($this->container);
        $register->setConnectBySession();

        return $register;
    }

    protected function getEntityGenerator()
    {
        $eg = new EntityGenerator($this->container, $this->getRegister());

        return $eg;
    }

    public function getGenerator($class)
    {
        $request = $this->getRequest();
        $kernel = $this->container->get('kernel');

        $eg = $this->getEntityGenerator();
//        $request->request->add(array('gene' => $eg->getGene(), 'bilan' => $register->getBilan()));
        $eg->dispatch(true);

        $classGenerator = 'Nicotec\DoctrineautoBundle\ClassVendor\Generator\\' . $class;

//        $root_dir = $this->get('kernel')->getRootDir();
//        echo $root_dir;

        $cg = new $classGenerator($request, $kernel);
//        $cg->setBilan($eg->getRegister()->getBilan());
//        var_dump($eg->getRegister()->getBilan());
//        var_dump($eg->getGene());
        $cg->setGene($eg->getGene());
        $cg->setPathClass($class);
//        echo '----' . $classGenerator;
//        die;
//        if($request->get('bilan')) {
        return $cg;
//        }
//        else {
//            $session->getFlashBag()->add('error', 'Erreur de configuration (table ou bilan)');
//
//            return die($this->redirect($this->generateUrl('gene.setup')));
//        }
    }

    /**
     * @Route("/copie", name="gene.copie")
     */
    public function copieAction()
    {
        $replace = false;
        $request = $this->getRequest();
//        echo $request->get('file').'--'.$request->get('path_class');
//        die;

        $pathfile = $this->get('kernel')->getRootDir() . '/../' . $request->get('file');
        $function = $request->get('function');


        $g = $this->getGenerator($request->get('path_class'));
        $codes = $g->{$function}();

        $spl = new SplFileObject($pathfile, 'w');
        if($spl->isReadable()) {
            $fs = new Filesystem();
            $file = $this->get('kernel')->getRootDir() . '/historique/' . date('YmdHis') . '.txt';
            $fs->copy($pathfile, $file);
            $replace = $file;
        }
        $spl->fwrite($codes['code']);

        return new JsonResponse(array(
            'file' => $request->get('file'),
            'erreur' => false,
            'replace' => $replace,
        ));
    }

}

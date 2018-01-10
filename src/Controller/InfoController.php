<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 29.12.17
 * Time: 07:58
 */

namespace App\Controller;


use LightningSale\LndRest\LndRestClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InfoController
 * @package App\Controller
 * @Route("/dashboard/info", name="info_")
 * @Security("is_granted('ROLE_CASHIER')")
 */
class InfoController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function infoAction(LndRestClient $lndClient): Response
    {
        $externalIp = $this->getParameter("external_ip");
        $externalPort = $this->getParameter("external_port");
        $info = $lndClient->getInfo();
        return $this->render("Info/info.html.twig", ['info' => $info, 'externalIp' => $externalIp, 'externalPort' => $externalPort]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 25.03.18
 * Time: 13:59
 */

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class FrontController
 * @package App\Controller
 * @Route("/front", name="front_")
 */
class FrontController extends Controller
{
    /**
     * @Route("/{url}", name="index", requirements={"url": ".*"})
     */
    public function indexAction(string $url = "")
    {
        return $this->render("react.html.twig", ['url' => $url]);
    }
}
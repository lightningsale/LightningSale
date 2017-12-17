<?php namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Created by PhpStorm.
 * User: richard
 * Date: 17.12.17
 * Time: 18:32
 */

class IndexController
{
    /**
     * @Route("/")
     */
    public function IndexAction(): Response {
        return new Response("Hello world!");
    }
}
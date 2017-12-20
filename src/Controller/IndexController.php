<?php namespace App\Controller;


use Grpc\Channel;
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
        $test = new Channel("127.0.0.1", []);
        dump($test->getConnectivityState(true));
        dump($test);
        return new Response("Hello world!");
    }
}
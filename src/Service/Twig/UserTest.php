<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 29.12.17
 * Time: 12:34
 */

namespace App\Service\Twig;


use App\Entity\Cashier;
use App\Entity\Merchant;

class UserTest extends \Twig_Extension
{

    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return \Twig_Test[]
     */
    public function getTests()
    {
        return [
            new \Twig_Test("Merchant", $this->instanceOfTest(Merchant::class)),
            new \Twig_Test("Cashier", $this->instanceOfTest(Cashier::class))
        ];
    }

    private function instanceOfTest($class): \Closure
    {
        return function($object) use ($class) {
            return $object instanceof $class;
        };
    }
}
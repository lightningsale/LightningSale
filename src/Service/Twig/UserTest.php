<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 29.12.17
 * Time: 12:34
 */

namespace App\Service\Twig;


use App\Entity\User;

class UserTest extends \Twig_Extension
{
    public function getTests()
    {
        return [
            new \Twig_Test("Merchant", function (User $user) {return $user->getRoles() === ["ROLE_MERCHANT"];}),
            new \Twig_Test("Cashier", function (User $user) {return $user->getRoles() === ["ROLE_CASHIER"];})
        ];
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 17:44
 */

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Cashier
 * @package App\Entity
 * @ORM\Entity()
 */
class Cashier extends User
{
    public function getRoles(): array
    {
        return ["ROLE_CASHIER"];
    }
}
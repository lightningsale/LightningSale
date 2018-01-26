<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 25.01.18
 * Time: 20:21
 */

namespace App\Form\Profile;


use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    /**
     * @var string
     * @Assert\Email()
     */
    public $email;

    /**
     * @var bool
     * @Assert\NotBlank()
     */
    public $role;
}
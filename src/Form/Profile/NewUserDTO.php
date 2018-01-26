<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 25.01.18
 * Time: 22:58
 */

namespace App\Form\Profile;

use Symfony\Component\Validator\Constraints as Assert;

class NewUserDTO
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

    /**
     * @var string
     * @Assert\Length(min="5")
     */
    public $newPassword;

    /**
     * @var string
     * @Assert\Expression(expression="this.newPassword == value", message="The password doesn't match")
     */
    public $repeatPassword;
}
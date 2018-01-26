<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 25.01.18
 * Time: 17:15
 */

namespace App\Form\Profile;


use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordDTO
{
    /**
     * @var string
     * @UserPassword()
     */
    public $oldPassword;

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
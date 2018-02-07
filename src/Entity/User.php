<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 15:19
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Entity
 * @ORM\MappedSuperclass()
 */
abstract class User implements UserInterface, EquatableInterface
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $email;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $type;

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function __construct(string $email,EncoderFactoryInterface $encoderFactory,  string $rawPassword, bool $admin)
    {
        $this->type = $admin ? 1 : 0;
        $this->email = $email;
        $this->createdAt = new \DateTime();
        $this->changePassword($encoderFactory, $rawPassword);
    }

    public function verifyPassword(EncoderFactoryInterface $encoderFactory, string $rawPassword): bool
    {
        $passwordEncoder = $encoderFactory->getEncoder(self::class);
        return $passwordEncoder->isPasswordValid($this->password, $rawPassword, $this->getSalt());
    }

    public function changeEmail(string $email)
    {
        $this->email = $email;
    }

    public function changePassword(EncoderFactoryInterface $encoderFactory, string $rawPassword): void
    {
        $passwordEncoder = $encoderFactory->getEncoder(self::class);
        $this->password = $passwordEncoder->encodePassword($rawPassword, $this->getSalt());
    }

    public function changeRole(bool $isAdmin)
    {
        $this->type = $isAdmin ? 1 : 0;
    }
    //region UserInterface
    public function getRoles(): array
    {
        switch ($this->type) {
            case 0: return ["ROLE_CASHIER"];
            case 1: return ["ROLE_MERCHANT"];
            default: throw new \DomainException("unkown user role ({$this->type})!");
        }
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials(){}
    //endregion

    public function isEqualTo(UserInterface $user)
    {
        return $user->getUsername() === $this->email && $user->getPassword() === $this->password;
    }
}
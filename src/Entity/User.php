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
 * @ORM\Table(name="users")
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type",type="integer")
 * @ORM\DiscriminatorMap({
 *      0 = "App\Entity\Cashier",
 *      1 = "App\Entity\Merchant"
 * })
 */
abstract class User implements UserInterface, EquatableInterface
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;
    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

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

    public function __construct(string $email,EncoderFactoryInterface $encoderFactory,  string $rawPassword)
    {
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

    //region UserInterface
    public function getRoles(): array
    {
        return ["ROLE_USER"];
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
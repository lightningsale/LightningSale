<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 15:19
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Entity
 * @ORM\Table("users")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type",type="integer")
 * @ORM\DiscriminatorMap({
 *      0: "App\Entity\Cashier",
 *      1: "App\Entity\Merchant"
 * })
 */
abstract class User implements UserInterface, \Serializable
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
     * @ORM\Column(type="string")
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

    public function __construct(string $email,PasswordEncoderInterface $passwordEncoder,  string $rawPassword)
    {
        $this->email = $email;
        $this->password = $passwordEncoder->encodePassword($rawPassword, $this->getSalt());
        $this->createdAt = new \DateTime();
    }

    public function getRoles(): array
    {
        return ["ROLE_USER"];
    }

    public function getPassword(): string
    {
        throw new \DomainException("This method should not be used!");
    }

    public function verifyPassword(PasswordEncoderInterface $passwordEncoder, string $rawPassword): bool
    {
        return $passwordEncoder->isPasswordValid($this->password, $rawPassword, $this->getSalt());
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

    public function serialize()
    {
        return serialize([$this->id,$this->email, $this->password]);
    }

    public function unserialize($serialized)
    {
        [$this->id,$this->email,$this->password] = $this->unserialize($serialized);
    }


}
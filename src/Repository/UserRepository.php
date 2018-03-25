<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 18:19
 */

namespace App\Repository;


use App\Entity\Cashier;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserRepository implements UserProviderInterface
{
    private $userRepo;

    /**
     * UserRepository constructor.
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->userRepo = $em->getRepository(Cashier::class);
    }

    public function find(int $id): ?User
    {
        return $this->userRepo->find($id);
    }

    public function loadUserByUsername($username): User
    {
        $user = $this->userRepo->findOneBy(['email' => $username]);
        if (!$user)
            throw new UsernameNotFoundException();

        return $user;
    }

    public function refreshUser(UserInterface $user): User
    {
        $email = $user->getUsername();
        if ($email === null)
            throw new UnsupportedUserException();

        $user = $this->userRepo->findOneBy(['email' => $email]);
        if (!$user)
            throw new UnsupportedUserException();

        return $user;
    }

    public function supportsClass($class)
    {
        return in_array($class, [User::class, Cashier::class], true);
    }

    /**
     * @return User[]|array
     */
    public function findAll()
    {
        return $this->userRepo->findAll();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepo->findOneBy(['email' => $email]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 14.01.18
 * Time: 12:33
 */

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TransactionsController
 * @package App\Controller
 * @Route("/dashboard/settings/users", name="users_")
 * @Security("is_granted('ROLE_MERCHANT')")
 */
class UsersController extends Controller
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @Route("/", name="index")
     */
    public function usersAction(): Response
    {
        return $this->render("Users/users.html.twig", [
            'users' => $this->userRepo->findAll()
        ]);
    }

    /**
     * @Route("/details/{email}", name="detail")
     */
    public function detailsAction(User $details): Response
    {
        return $this->render("Users/detail.html.twig", [
            'user' => $details,
        ]);
    }
}
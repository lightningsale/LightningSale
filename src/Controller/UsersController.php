<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 14.01.18
 * Time: 12:33
 */

namespace App\Controller;


use App\Entity\Cashier;
use App\Form\Profile\NewUserDTO;
use App\Form\Profile\NewUserType;
use App\Form\Profile\UserDTO;
use App\Form\Profile\UserType;
use App\Repository\LndInvoiceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use LightningSale\LndClient\Model\Invoice;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TransactionsController
 * @package App\Controller
 * @Route("/dashboard/settings/users", name="users_")
 * @Security("is_granted('ROLE_MERCHANT')")
 */
class UsersController extends Controller
{
    private $userRepo;
    private $em;
    private $lndInvoiceRepo;

    public function __construct(UserRepository $userRepo, EntityManagerInterface $em, LndInvoiceRepository $lndInvoiceRepository)
    {
        $this->userRepo = $userRepo;
        $this->em = $em;
        $this->lndInvoiceRepo = $lndInvoiceRepository;
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
    public function detailsAction(Cashier $cashier, Request $request, UserInterface $user): Response
    {
        /** @var Invoice[] $invoices */
        $invoices = $this->lndInvoiceRepo->findByUser($cashier);
        $form = $this->createForm(UserType::class, null, ['user' => $cashier]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user === $cashier) {
                $this->addFlash("warning","You can't change your own user");
            } else {
                /** @var UserDTO $data */
                $data = $form->getData();
                $cashier->changeEmail($data->email);
                $cashier->changeRole($data->role);
                $this->em->flush();
                $this->addFlash("success", "User updated");
            }
        }


        return $this->render("Users/detail.html.twig", [
            'user' => $cashier,
            'invoices' => $invoices,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function newUserAction(Request $request, EncoderFactoryInterface $encoderFactory) {
        $form = $this->createForm(NewUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var NewUserDTO $data */
            $data = $form->getData();
            $user = new Cashier($data->email, $encoderFactory, $data->newPassword, $data->role);

            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash("success", "User {$data->email} is created");
            return $this->redirectToRoute("users_index");
        }

        return $this->render("Users/new_user.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
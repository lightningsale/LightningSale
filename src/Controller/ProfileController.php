<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 14.01.18
 * Time: 13:59
 */

namespace App\Controller;
use App\Entity\Cashier;
use App\Form\Profile\ChangePasswordDTO;
use App\Form\Profile\ChangePasswordType;
use App\Form\Profile\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ProfileController
 * @package App\Controller
 * @Route("/dashboard/profile", name="profile_")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/", name="index")
     * @param Cashier $user
     */
    public function profileAction(Request $request, UserInterface $user, EntityManagerInterface $em, EncoderFactoryInterface $encoderFactory): Response
    {
        $emailForm = $this->createForm(ProfileType::class);
        $emailForm->handleRequest($request);
        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $data = $emailForm->getData();
            $email = $data['email'];
            $user->changeEmail($email);
            $em->flush();
            $this->addFlash("success", "Email changed");
        }

        $changePasswordForm = $this->createForm(ChangePasswordType::class);
        $changePasswordForm->handleRequest($request);
        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            /** @var ChangePasswordDTO $DTO */
            $DTO = $changePasswordForm->getData();
            $user->changePassword($encoderFactory, $DTO->newPassword);
            $this->addFlash("success", "Password changed");
            $em->flush();
        }

        return $this->render("Profile/profile.html.twig", [
            'user' => $user,
            'emailForm' => $emailForm->createView(),
            'changePasswordForm' => $changePasswordForm->createView(),
        ]);
    }
}
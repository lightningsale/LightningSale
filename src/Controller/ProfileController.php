<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 14.01.18
 * Time: 13:59
 */

namespace App\Controller;
use App\Entity\Cashier;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function profileAction(UserInterface $user): Response
    {
        $emailForm = $this->createForm(ProfileType::class);
        $changePasswordForm = $this->createForm(ChangePasswordType::class);

        return $this->render("Profile/profile.html.twig", [
            'user' => $user,
            'emailForm' => $emailForm->createView(),
            'changePasswordForm' => $changePasswordForm->createView(),
        ]);
    }

    /**
     * @param Cashier $user
     * @Route("/change_email", name="change_email")
     */
    public function changeEmailAction(UserInterface $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProfileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->changeEmail($data['email']);
            $em->flush();
            $this->addFlash("success", "Email changed");
        }


        foreach ($form->getErrors() as $error) {
            $this->addFlash("warning", $error->getMessage());
        }

        return $this->redirectToRoute("profile_index");
    }
}
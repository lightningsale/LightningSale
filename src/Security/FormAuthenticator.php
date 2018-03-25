<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 27.12.17
 * Time: 20:51
 */

namespace App\Security;


use App\Entity\User;
use App\Form\Security\LoginType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class FormAuthenticator extends AbstractGuardAuthenticator
{

    private $encoderFactory;
    private $router;
    private $session;
    private $formBuilder;
    private $userRepo;
    private $csrfTokenManager;

    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        RouterInterface $router,
        SessionInterface $session,
        FormFactoryInterface $formBuilder,
        UserRepository $userRepo,
        CsrfTokenManagerInterface $csrfTokenManager
    ){
        $this->encoderFactory = $encoderFactory;
        $this->router = $router;
        $this->session = $session;
        $this->formBuilder = $formBuilder;
        $this->userRepo = $userRepo;
        $this->csrfTokenManager = $csrfTokenManager;
    }


    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate("login_merchant"));
    }

    public function supports(Request $request)
    {
        return $request->getPathInfo() === '/login_check';
    }

    public function getCredentials(Request $request)
    {
        $form = $this->formBuilder->create(LoginType::class);
        $form->handleRequest($request);
        $credentials = $form->getData();

        $this->session->set(Security::AUTHENTICATION_ERROR, $credentials['username']);
        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->userRepo->loadUserByUsername($credentials['username']);
    }

    /**
     * @param array $credentials
     * @param User $user
     * @return bool|UserInterface
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $user->verifyPassword($this->encoderFactory, $credentials['password']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $this->session->set(Security::AUTHENTICATION_ERROR, "No user with that password found.");
        return new RedirectResponse($this->router->generate("login_merchant"));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->router->generate("front_index"));
    }

    public function supportsRememberMe()
    {
        return true;
    }
}
<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private HttpClientInterface $httpClient;
    private string $recaptchaSecret;
    private UserRepository $userRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, HttpClientInterface $httpClient, UserRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->httpClient = $httpClient;
        $this->recaptchaSecret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
        $this->userRepository = $userRepository;
    }

    private function verifyRecaptcha(?string $token): bool
    {
        if (!$token) {
            return false;
        }
        
        try {
            $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'body' => [
                    'secret' => $this->recaptchaSecret,
                    'response' => $token,
                ],
            ]);

            $result = $response->toArray();
            return $result['success'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('_username', '');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token', '');
        
        // Verify reCAPTCHA
        $recaptchaResponse = $request->request->get('g-recaptcha-response');
        if (!$this->verifyRecaptcha($recaptchaResponse)) {
            throw new CustomUserMessageAuthenticationException('La vérification reCAPTCHA a échoué. Veuillez réessayer.');
        }
        
        // Check if user is banned (inactive)
        $user = $this->userRepository->findOneBy(['emailUser' => $username]);
        if ($user && $user->getStatusUser() === 'inactive') {
            throw new CustomUserMessageAuthenticationException('Votre compte a été désactivé. Veuillez contacter un administrateur.');
        }
        
        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Get the user's roles from the token
        $roles = $token->getRoleNames();
        
        // Check if user has ROLE_ADMIN
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }
        
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_profile_show'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
} 
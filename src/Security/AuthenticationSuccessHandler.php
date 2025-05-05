<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $urlGenerator;    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        // Get the user's roles from the token
        $roles = $token->getRoleNames();        // Check if user has ROLE_ADMIN
        if (in_array('ROLE_ADMIN', $roles)) {
            error_log('Admin role detected, redirecting to admin dashboard');
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }

        // Default redirect for all other users
        return new RedirectResponse($this->urlGenerator->generate('app_profile_show'));
    }
}

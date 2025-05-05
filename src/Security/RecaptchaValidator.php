<?php

namespace App\Security;

use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\RequestStack;

class RecaptchaValidator
{
    private $recaptcha;
    private $requestStack;
    private $recaptchaSecret;

    public function __construct(
        string $recaptchaSecret,
        RequestStack $requestStack
    ) {
        $this->recaptchaSecret = $recaptchaSecret;
        $this->recaptcha = new ReCaptcha($recaptchaSecret);
        $this->requestStack = $requestStack;
    }

    public function verify(?string $recaptchaResponse): bool
    {
        if (empty($recaptchaResponse)) {
            return false;
        }

        $request = $this->requestStack->getCurrentRequest();
        $ip = $request ? $request->getClientIp() : '';

        $response = $this->recaptcha
            ->setExpectedHostname($request ? $request->getHost() : '')
            ->verify($recaptchaResponse, $ip);

        return $response->isSuccess();
    }
}

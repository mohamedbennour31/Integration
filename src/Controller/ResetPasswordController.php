<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        error_log('Entering password reset request handler');
        
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            error_log('Form submitted and valid');
            $email = $form->get('emailUser')->getData();
            error_log('Email submitted: ' . $email);
            
            try {
                return $this->processSendingPasswordResetEmail(
                    $email,
                    $mailer,
                    $translator
                );
            } catch (\Exception $e) {
                error_log('Error in request handler: ' . $e->getMessage());
                error_log('Stack trace: ' . $e->getTraceAsString());
                $this->addFlash('reset_password_error', $translator->trans($e->getMessage()));
                return $this->redirectToRoute('app_check_email');
            }
        }
        
        if ($form->isSubmitted()) {
            error_log('Form submitted but not valid');
            foreach ($form->getErrors(true) as $error) {
                error_log('Form error: ' . $error->getMessage());
            }
        } else {
            error_log('Form not submitted');            
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, ?string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode(hash) the plain password, and set it.
            $user->setMdpUser($passwordHasher->hashPassword($user, $plainPassword));
            $this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer, TranslatorInterface $translator): RedirectResponse
    {
        error_log('Starting processSendingPasswordResetEmail');
        error_log('Looking for user with email: ' . $emailFormData);
        error_log('Current working directory: ' . getcwd());
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'emailUser' => $emailFormData,
        ]);
        
        if ($user === null) {
            error_log('No user found with email: ' . $emailFormData);
        } else {
            error_log('User found with email: ' . $emailFormData);
        }
        
        error_log("Attempting to process password reset for email: " . $emailFormData);
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'emailUser' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            error_log("User not found for email: " . $emailFormData);
            return $this->redirectToRoute('app_check_email');
        }
        
        error_log("User found, proceeding with reset token generation");

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // Uncomment these lines to help debug the issue
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_check_email');
        }

        $resetUrl = $this->generateUrl('app_reset_password', 
            ['token' => $resetToken->getToken()], 
            \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
        );

        // First try with a simple email to verify sending works
        $email = (new Email())
            ->from('ttttfarah@gmail.com')
            ->to($user->getEmailUser())
            ->subject('Réinitialisation de votre mot de passe HACKIFY')
            ->html(sprintf(
                '<p>Pour réinitialiser votre mot de passe, cliquez sur ce lien: <a href="%s">Réinitialiser le mot de passe</a></p><p>Ce lien expirera dans %d heures.</p>',
                $resetUrl,
                $this->resetPasswordHelper->getTokenLifetime() / 3600
            ))
            ->priority(Email::PRIORITY_HIGH)
        ;
        try {
            error_log("Starting password reset process for: " . $user->getEmailUser());
            error_log("Reset URL generated: " . $resetUrl);
            error_log("Token lifetime: " . $this->resetPasswordHelper->getTokenLifetime());
            error_log("Preparing email with Mailtrap configuration...");
            error_log("MAILER_DSN: " . $_ENV['MAILER_DSN']);
            
            error_log('Creating password reset email...');
            error_log('Reset URL: ' . $resetUrl);
            
            // Create transport with working configuration
            $dsn = 'smtp://ttttfarah%40gmail.com:pjzp%20fqie%20epci%20wkmk@smtp.gmail.com:587?encryption=tls&auth_mode=login';
            $transport = Transport::fromDsn($dsn);
            $customMailer = new Mailer($transport);
            
            // Create email
            $email = (new Email())
                ->from(new Address('ttttfarah@gmail.com', 'HACKIFY'))
                ->to($user->getEmailUser())
                ->subject('Réinitialisation de votre mot de passe HACKIFY')
                ->text('Pour réinitialiser votre mot de passe, cliquez sur ce lien: ' . $resetUrl)
                ->html(
                    '<p>Pour réinitialiser votre mot de passe, cliquez sur ce lien: ' .
                    '<a href="' . $resetUrl . '">Réinitialiser le mot de passe</a></p>' .
                    '<p>Ce lien expirera dans ' . ($this->resetPasswordHelper->getTokenLifetime() / 3600) . ' heures.</p>'
                )
                ->priority(Email::PRIORITY_HIGH);
            
            error_log('Attempting to send password reset email...');
            try {
                error_log('Using custom mailer with working DSN configuration');
                $customMailer->send($email);
                error_log('Reset email sent successfully');
                error_log('Email was sent to: ' . $user->getEmailUser());
            } catch (\Exception $e) {
                error_log('Error sending email: ' . $e->getMessage());
                error_log('Stack trace: ' . $e->getTraceAsString());
                throw $e;
            }
            
            $this->addFlash('success', 'Email de réinitialisation envoyé avec succès.');
        } catch (\Exception $e) {
            $errorDetails = sprintf(
                "Email Error:\nMessage: %s\nFile: %s\nLine: %d\nTrace:\n%s",
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );
            error_log($errorDetails);
            
            $this->addFlash('reset_password_error', 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer plus tard.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}

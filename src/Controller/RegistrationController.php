<?php
namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Convert the Enum role to an array of roles
            /** @var UserRole $roleEnum */
            $roleEnum = $form->get('roleUser')->getData();
            $user->setRoleUser([$roleEnum->value]);
            
            // Handle password
            $user->setMdpUser(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Handle profile picture
            $profilePicture = $form->get('profile_picture')->getData();
            if ($profilePicture) {
                $originalFilename = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePicture->guessExtension();

                try {
                    $profilePicture->move(
                        $this->getParameter('profile_pictures_directory'),
                        $newFilename
                    );
                    $user->setPhotoUser($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de votre photo de profil.');
                }
            }

            // Set telephone number
            $telUser = $form->get('telUser')->getData();
            if ($telUser) {
                $user->setTelUser($telUser);
            }

            // Set default status
            $user->setStatusUser('active');

            $entityManager->persist($user);
            $entityManager->flush();

            // Add flash message for success
            $this->addFlash('success', 'Votre compte a été créé avec succès !');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

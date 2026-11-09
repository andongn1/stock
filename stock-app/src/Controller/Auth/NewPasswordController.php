<?php

namespace App\Controller\Auth;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewPasswordController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/reset-password/{token}', name: 'password.reset', methods: ['GET'])]
    public function create(string $token, Request $request): Response
    {
        return $this->render('auth/reset-password.html.twig', [
            'token' => $token,
            'email' => $request->query->get('email', ''),
        ]);
    }

    #[Route('/reset-password', name: 'password.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $token = $request->request->get('token', '');
        $email = strtolower(trim($request->request->get('email', '')));
        $password = $request->request->get('password', '');
        $passwordConfirmation = $request->request->get('password_confirmation', '');

        // Validate
        $violations = $this->validator->validate([
            'token' => $token,
            'email' => $email,
            'password' => $password,
        ], new Assert\Collection([
            'token' => [new Assert\NotBlank(message: 'Le token est obligatoire.')],
            'email' => [
                new Assert\NotBlank(message: 'L\'email est obligatoire.'),
                new Assert\Email(mode: 'html5', message: 'Veuillez entrer une adresse email valide.'),
            ],
            'password' => [
                new Assert\NotBlank(message: 'Le mot de passe est obligatoire.'),
                new Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'),
            ],
        ]));

        if ($password !== $passwordConfirmation) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            return $this->redirectToRoute('password.reset', ['token' => $token, 'email' => $email]);
        }

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $this->addFlash('error', $violation->getMessage());
            }
            return $this->redirectToRoute('password.reset', ['token' => $token, 'email' => $email]);
        }

        // Verify token
        $tokenHash = hash('sha256', $token);
        $passwordResetToken = $this->entityManager->getRepository(PasswordResetToken::class)
            ->findOneBy(['email' => $email, 'token' => $tokenHash]);

        if (!$passwordResetToken) {
            $this->addFlash('error', 'Le token de réinitialisation est invalide ou a expiré.');
            return $this->redirectToRoute('password.request');
        }

        // Check token age (60 minutes)
        $createdAt = $passwordResetToken->getCreatedAt();
        if ($createdAt && (new \DateTime())->getTimestamp() - $createdAt->getTimestamp() > 3600) {
            $this->entityManager->remove($passwordResetToken);
            $this->entityManager->flush();
            $this->addFlash('error', 'Le token de réinitialisation a expiré.');
            return $this->redirectToRoute('password.request');
        }

        // Find user and update password
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('password.request');
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setUpdatedAt(new \DateTime());

        // Delete the token
        $this->entityManager->remove($passwordResetToken);
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès !');

        return $this->redirectToRoute('login');
    }
}

<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticationManagerInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/', name: 'profile.edit', methods: ['GET'])]
    public function edit(Request $request): Response
    {
        return $this->render('profile/edit.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/', name: 'profile.update', methods: ['PATCH', 'POST'])]
    public function update(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        $name = trim($request->request->get('name', ''));
        $email = strtolower(trim($request->request->get('email', '')));

        // Validate
        $violations = $this->validator->validate([
            'name' => $name,
            'email' => $email,
        ], new Assert\Collection([
            'name' => [
                new Assert\NotBlank(message: 'Le nom est obligatoire.'),
                new Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'),
            ],
            'email' => [
                new Assert\NotBlank(message: 'L\'email est obligatoire.'),
                new Assert\Email(mode: 'html5', message: 'Veuillez entrer une adresse email valide.'),
                new Assert\Length(max: 255, maxMessage: 'L\'email ne peut pas dépasser {{ limit }} caractères.'),
            ],
        ]));

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $this->addFlash('error', $violation->getMessage());
            }
            return $this->redirectToRoute('profile.edit');
        }

        // Check email uniqueness (excluding current user)
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);
        if ($existingUser && $existingUser->getId() !== $user->getId()) {
            $this->addFlash('error', 'Cet email est déjà utilisé par un autre compte.');
            return $this->redirectToRoute('profile.edit');
        }

        $emailChanged = $user->getEmail() !== $email;

        $user->setName($name);
        $user->setEmail($email);

        if ($emailChanged) {
            $user->setEmailVerifiedAt(null);
        }

        $user->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();

        $this->addFlash('success', 'Profil mis à jour avec succès !');

        return $this->redirectToRoute('profile.edit');
    }

    #[Route('/', name: 'profile.destroy', methods: ['DELETE'])]
    public function destroy(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        $password = $request->request->get('password', '');

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            $this->addFlash('error', 'Le mot de passe est incorrect.');
            return $this->redirectToRoute('profile.edit');
        }

        // Logout
        $this->container->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        // Delete user
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre compte a été supprimé.');

        return $this->redirectToRoute('app_root');
    }
}

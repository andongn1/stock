<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/password', name: 'password.update', methods: ['PUT', 'POST'])]
    public function update(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        $currentPassword = $request->request->get('current_password', '');
        $newPassword = $request->request->get('password', '');
        $passwordConfirmation = $request->request->get('password_confirmation', '');

        // Validate current password
        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            return $this->redirectToRoute('profile.edit');
        }

        // Validate new password
        $violations = $this->validator->validate($newPassword, [
            new Assert\NotBlank(message: 'Le nouveau mot de passe est obligatoire.'),
            new Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'),
        ]);

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $this->addFlash('error', $violation->getMessage());
            }
            return $this->redirectToRoute('profile.edit');
        }

        if ($newPassword !== $passwordConfirmation) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            return $this->redirectToRoute('profile.edit');
        }

        // Update password
        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $user->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();

        $this->addFlash('success', 'Mot de passe mis à jour avec succès !');

        return $this->redirectToRoute('profile.edit');
    }
}

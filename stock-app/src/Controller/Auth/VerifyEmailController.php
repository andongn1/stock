<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VerifyEmailController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/verify-email/{id}/{hash}', name: 'verification.verify', methods: ['GET'])]
    public function __invoke(Request $request, string $id, string $hash): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        // Verify the hash matches the user's email
        $expectedHash = hash('sha256', $user->getEmail());

        if (!hash_equals($expectedHash, $hash) || (string) $user->getId() !== $id) {
            $this->addFlash('error', 'Le lien de vérification est invalide.');
            return $this->redirectToRoute('verification.notice');
        }

        if ($user->getEmailVerifiedAt() !== null) {
            $this->addFlash('success', 'Votre email est déjà vérifié.');
            return $this->redirectToRoute('dashboard');
        }

        $user->setEmailVerifiedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre adresse email a été vérifiée avec succès !');

        return $this->redirectToRoute('dashboard');
    }
}

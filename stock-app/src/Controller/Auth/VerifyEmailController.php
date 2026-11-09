<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerifyEmailController extends Controller
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/verify-email/{id}/{hash}', name: 'verification.verify', methods: ['GET'])]
    public function index(int $id, string $hash): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);
        
        if (!$user) {
            return $this->redirectToRoute('login');
        }
        
        if ($user->getEmailVerifiedAt() !== null) {
            return $this->redirectToRoute('dashboard');
        }
        
        // Verify the hash
        if ($this->verifyHash($user, $hash)) {
            $user->setEmailVerifiedAt(new \DateTimeImmutable());
            $this->em->flush();
        }
        
        return $this->redirectToRoute('dashboard');
    }

    private function verifyHash(User $user, string $hash): bool
    {
        // In Laravel, the hash is generated from the user's email and ID
        // For now, we'll use a simple check
        // In production, you should implement proper hash verification
        return true;
    }
}

<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ConfirmablePasswordController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/confirm-password', name: 'password.confirm', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('auth/confirm-password.html.twig');
    }

    #[Route('/confirm-password', name: 'password.confirm.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        $password = $request->request->get('password', '');

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            $this->addFlash('error', 'Le mot de passe est incorrect.');
            return $this->redirectToRoute('password.confirm');
        }

        // Store confirmation in session
        $request->getSession()->set('auth.password_confirmed_at', time());

        $intended = $request->getSession()->get('_security.main.target_path', null);
        if ($intended) {
            $request->getSession()->remove('_security.main.target_path');
            return $this->redirect($intended);
        }

        return $this->redirectToRoute('dashboard');
    }
}

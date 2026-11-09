<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Form\PasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmablePasswordController extends Controller
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    #[Route('/confirm-password', name: 'password.confirm', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('auth/confirm-password.html.twig');
    }

    #[Route('/confirm-password', name: 'password.confirm.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $form = $this->createForm(PasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->getUser();
            
            // Verify current password
            if (!$this->passwordHasher->isPasswordValid($user, $data['current_password'])) {
                return $this->render('auth/confirm-password.html.twig', [
                    'form' => $form,
                    'error' => 'Le mot de passe est incorrect.',
                ]);
            }
            
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('auth/confirm-password.html.twig', [
            'form' => $form,
        ]);
    }
}

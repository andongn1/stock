<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Form\PasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends Controller
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    #[Route('/password', name: 'password.update', methods: ['PUT', 'POST'])]
    public function update(Request $request): Response
    {
        $form = $this->createForm(PasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->getUser();
            
            // Verify current password
            if (!$this->passwordHasher->isPasswordValid($user, $data['current_password'])) {
                return $this->render('auth/password.html.twig', [
                    'form' => $form,
                    'error' => 'Le mot de passe actuel est incorrect.',
                ]);
            }
            
            // Update password
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['new_password']));
            
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('auth/password.html.twig', [
            'form' => $form,
        ]);
    }
}

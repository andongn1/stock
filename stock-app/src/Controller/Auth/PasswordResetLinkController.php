<?php

namespace App\Controller\Auth;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordResetLinkController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private ValidatorInterface $validator,
        private MailerInterface $mailer
    ) {
    }

    #[Route('/forgot-password', name: 'password.request', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('auth/forgot-password.html.twig');
    }

    #[Route('/forgot-password', name: 'password.email', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $email = $request->request->get('email', '');

        $violations = $this->validator->validate($email, [
            new Assert\NotBlank(message: 'L\'email est obligatoire.'),
            new Assert\Email(mode: 'html5', message: 'Veuillez entrer une adresse email valide.'),
        ]);

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $this->addFlash('error', $violation->getMessage());
            }
            return $this->redirectToRoute('password.request');
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $this->addFlash('error', 'Nous ne trouvons pas d\'utilisateur avec cette adresse email.');
            return $this->redirectToRoute('password.request');
        }

        // Generate token
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        // Delete existing tokens for this email
        $existingTokens = $this->entityManager->getRepository(PasswordResetToken::class)
            ->findBy(['email' => $email]);
        foreach ($existingTokens as $existingToken) {
            $this->entityManager->remove($existingToken);
        }

        // Create new token
        $passwordResetToken = new PasswordResetToken();
        $passwordResetToken->setEmail($email);
        $passwordResetToken->setToken($tokenHash);
        $passwordResetToken->setCreatedAt(new \DateTime());
        $this->entityManager->persist($passwordResetToken);
        $this->entityManager->flush();

        // Send email
        $resetUrl = $this->generateUrl('password.reset', ['token' => $token], true);

        try {
            $emailMessage = (new TemplatedEmail())
                ->from(new Address('noreply@stock.local', 'Stock Management'))
                ->to($user->getEmail())
                ->subject('Réinitialisation de votre mot de passe')
                ->htmlTemplate('emails/password-reset.html.twig')
                ->context([
                    'resetUrl' => $resetUrl,
                    'user' => $user,
                ]);

            $this->mailer->send($emailMessage);
        } catch (\Exception $e) {
            // Log error but don't expose it to user
        }

        $this->addFlash('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');

        return $this->redirectToRoute('login');
    }
}

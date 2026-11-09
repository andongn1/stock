<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailVerificationNotificationController extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[Route('/email/verification-notification', name: 'verification.send', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        if ($user->getEmailVerifiedAt() !== null) {
            return $this->redirectToRoute('dashboard');
        }

        // Generate signed verification URL
        $hash = hash('sha256', $user->getEmail());
        $verificationUrl = $this->urlGenerator->generate('verification.verify', [
            'id' => $user->getId(),
            'hash' => $hash,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        try {
            $email = (new TemplatedEmail())
                ->from(new Address('noreply@stock.local', 'Stock Management'))
                ->to($user->getEmail())
                ->subject('Vérifiez votre adresse email')
                ->htmlTemplate('emails/verify-email.html.twig')
                ->context([
                    'verificationUrl' => $verificationUrl,
                    'user' => $user,
                ]);

            $this->mailer->send($email);
        } catch (\Exception $e) {
            // Log but don't expose
        }

        $this->addFlash('success', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');

        return $this->redirectToRoute('verification.notice');
    }
}

<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisteredUserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/register', name: 'register', methods: ['GET'])]
    public function create(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('auth/register.html.twig');
    }

    #[Route('/register', name: 'register.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $name = $request->request->get('name', '');
        $email = strtolower(trim($request->request->get('email', '')));
        $password = $request->request->get('password', '');
        $passwordConfirmation = $request->request->get('password_confirmation', '');

        $errors = [];

        // Validate name
        $nameConstraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank(message: 'Le nom est obligatoire.'),
                new Assert\Type(type: 'string'),
                new Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'),
            ],
        ]);

        $nameViolations = $this->validator->validate(['name' => $name], $nameConstraints);
        foreach ($nameViolations as $violation) {
            $errors['name'] = $violation->getMessage();
        }

        // Validate email
        $emailConstraints = new Assert\Collection([
            'email' => [
                new Assert\NotBlank(message: 'L\'email est obligatoire.'),
                new Assert\Email(mode: 'html5', message: 'Veuillez entrer une adresse email valide.'),
                new Assert\Length(max: 255, maxMessage: 'L\'email ne peut pas dépasser {{ limit }} caractères.'),
            ],
        ]);

        $emailViolations = $this->validator->validate(['email' => $email], $emailConstraints);
        foreach ($emailViolations as $violation) {
            $errors['email'] = $violation->getMessage();
        }

        // Check email uniqueness
        if (empty($errors['email']) && $this->userRepository->findOneBy(['email' => $email])) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        // Validate password
        if (empty($password)) {
            $errors['password'] = 'Le mot de passe est obligatoire.';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif ($password !== $passwordConfirmation) {
            $errors['password'] = 'Les mots de passe ne correspondent pas.';
        }

        if (!empty($errors)) {
            foreach ($errors as $field => $message) {
                $this->addFlash('error', $message);
            }
            return $this->render('auth/register.html.twig', [
                'old' => ['name' => $name, 'email' => $email],
            ]);
        }

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRole(UserRole::USER);
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Login the user
        $request->getSession()->set('_security_main', serialize([
            'authenticated' => true,
            'user' => $user->getEmail(),
        ]));

        // Use the security system to login
        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
            $user,
            'main',
            $user->getRoles()
        );
        $this->container->get('security.token_storage')->setToken($token);
        $request->getSession()->set('_security_main', serialize($token));

        $this->addFlash('success', 'Compte créé avec succès !');

        return $this->redirectToRoute('dashboard');
    }
}

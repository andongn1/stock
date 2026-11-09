<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Enum\UserRole;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/', name: 'users.index', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->userRepository->findBy([], ['name' => 'ASC']);

        return $this->render('users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/create', name: 'users.create', methods: ['GET'])]
    public function create(): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => false,
            'include_password' => true,
        ]);

        $roles = [UserRole::ADMIN, UserRole::USER];

        return $this->render('users/create.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'roles' => $roles,
        ]);
    }

    #[Route('/', name: 'users.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => false,
            'include_password' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $request->request->get('user')['plainPassword'] ?? null;
            
            if ($plainPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur cree avec succes !');

            return $this->redirectToRoute('users.index');
        }

        $roles = [UserRole::ADMIN, UserRole::USER];

        return $this->render('users/create.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'roles' => $roles,
        ]);
    }

    #[Route('/{id}', name: 'users.show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'users.edit', methods: ['GET'])]
    public function edit(User $user): Response
    {
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true,
            'include_password' => false,
        ]);

        $roles = [UserRole::ADMIN, UserRole::USER];

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'roles' => $roles,
        ]);
    }

    #[Route('/{id}', name: 'users.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true,
            'include_password' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->get('user') ?? [];
            
            $user->setName($data['name'] ?? $user->getName());
            $user->setEmail($data['email'] ?? $user->getEmail());
            
            if (isset($data['role'])) {
                $role = UserRole::from($data['role']);
                $user->setRole($role);
            }
            
            if (!empty($data['plainPassword'])) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $data['plainPassword']);
                $user->setPassword($hashedPassword);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur mis a jour avec succes !');

            return $this->redirectToRoute('users.index');
        }

        $roles = [UserRole::ADMIN, UserRole::USER];

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'roles' => $roles,
        ]);
    }

    #[Route('/{id}', name: 'users.destroy', methods: ['DELETE'])]
    public function destroy(User $user): RedirectResponse
    {
        $currentUser = $this->getUser();

        if ($currentUser && $user->getId() === $currentUser->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte !');
            return $this->redirectToRoute('users.index');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprime avec succes !');

        return $this->redirectToRoute('users.index');
    }
}

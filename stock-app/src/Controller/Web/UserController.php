<?php

namespace App\Controller\Web;

use App\Controller\Controller;
use App\Data\User\CreateUserDto;
use App\Data\User\UpdateUserDto;
use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/users')]
class UserController extends Controller
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('', name: 'users.index', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findBy([], ['name' => 'ASC']);

        return $this->render('users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/create', name: 'users.create', methods: ['GET'])]
    public function create(): Response
    {
        $roles = [UserRole::Admin->value, UserRole::User->value];

        return $this->render('users/create.html.twig', [
            'roles' => $roles,
        ]);
    }

    #[Route('', name: 'users.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $dto = new CreateUserDto();
        $this->serializer->deserialize($request->getContent(), CreateUserDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $user = new User();
        $user->setName($dto->name);
        $user->setEmail($dto->email);
        $user->setRole(UserRole::from($dto->role));
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));

        $this->em->persist($user);
        $this->em->flush();

        return $this->redirectToRoute('users.index');
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
        $roles = [UserRole::Admin->value, UserRole::User->value];

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    #[Route('/{id}', name: 'users.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, User $user): Response
    {
        $dto = new UpdateUserDto();
        $this->serializer->deserialize($request->getContent(), UpdateUserDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $user->setName($dto->name);
        $user->setEmail($dto->email);
        $user->setRole(UserRole::from($dto->role));

        if ($dto->password) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));
        }

        $this->em->flush();

        return $this->redirectToRoute('users.index');
    }

    #[Route('/{id}', name: 'users.destroy', methods: ['DELETE'])]
    public function destroy(User $user): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Prevent deleting own account
        if ($user->getId() === $currentUser->getId()) {
            return $this->redirectToRoute('users.index');
        }

        $this->em->remove($user);
        $this->em->flush();

        return $this->redirectToRoute('users.index');
    }
}

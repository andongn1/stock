<?php

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Entity\PersonalAccessToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api')]
class TokenController extends Controller
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/user', name: 'api.user', methods: ['GET'])]
    public function user(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()->value,
        ]);
    }

    #[Route('/tokens', name: 'api.tokens.index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $tokens = $this->em->getRepository(PersonalAccessToken::class)
            ->findBy(['tokenableType' => User::class, 'tokenableId' => $user->getId()]);
        
        $data = array_map(function(PersonalAccessToken $token) {
            return [
                'id' => $token->getId(),
                'name' => $token->getName(),
                'token' => $token->getToken(),
                'abilities' => $token->getAbilities(),
                'created_at' => $token->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $token->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $tokens);
        
        return $this->json($data);
    }

    #[Route('/tokens', name: 'api.tokens.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $name = $request->request->get('name', 'Personal Access Token');
        $abilities = $request->request->all('abilities', []);
        
        // Generate a random token
        $plainTextToken = bin2hex(random_bytes(40));
        
        $token = new PersonalAccessToken();
        $token->setName($name);
        $token->setToken(hash('sha256', $plainTextToken));
        $token->setAbilities($abilities);
        $token->setTokenableType(User::class);
        $token->setTokenableId($user->getId());
        
        $this->em->persist($token);
        $this->em->flush();
        
        return $this->json([
            'id' => $token->getId(),
            'name' => $token->getName(),
            'token' => $plainTextToken,
            'abilities' => $token->getAbilities(),
            'created_at' => $token->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $token->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }

    #[Route('/tokens/{id}', name: 'api.tokens.destroy', methods: ['DELETE'])]
    public function destroy(PersonalAccessToken $token): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        if ($token->getTokenableId() !== $user->getId() || $token->getTokenableType() !== User::class) {
            return new JsonResponse(['message' => 'Unauthorized'], 403);
        }
        
        $this->em->remove($token);
        $this->em->flush();
        
        return new JsonResponse(null, 204);
    }
}

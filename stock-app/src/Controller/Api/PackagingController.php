<?php

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Data\Packaging\CreatePackagingDto;
use App\Data\Packaging\UpdatePackagingDto;
use App\Entity\Packaging;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/packagings')]
class PackagingController extends Controller
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api.packagings.index', methods: ['GET'])]
    public function index(): Response
    {
        $packagings = $this->em->getRepository(Packaging::class)->findBy([], ['createdAt' => 'DESC']);
        
        $data = array_map(function(Packaging $packaging) {
            return [
                'id' => $packaging->getId(),
                'name' => $packaging->getName(),
                'articles_count' => $packaging->getArticles()->count(),
                'created_at' => $packaging->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $packaging->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $packagings);

        return $this->json($data);
    }

    #[Route('', name: 'api.packagings.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $dto = new CreatePackagingDto();
        $this->serializer->deserialize($request->getContent(), CreatePackagingDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $packaging = new Packaging();
        $packaging->setName($dto->name);
        
        $this->em->persist($packaging);
        $this->em->flush();

        return $this->json([
            'id' => $packaging->getId(),
            'name' => $packaging->getName(),
            'articles_count' => 0,
            'created_at' => $packaging->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $packaging->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }

    #[Route('/{id}', name: 'api.packagings.show', methods: ['GET'])]
    public function show(Packaging $packaging): Response
    {
        return $this->json([
            'id' => $packaging->getId(),
            'name' => $packaging->getName(),
            'articles_count' => $packaging->getArticles()->count(),
            'created_at' => $packaging->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $packaging->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.packagings.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Packaging $packaging): Response
    {
        $dto = new UpdatePackagingDto();
        $this->serializer->deserialize($request->getContent(), UpdatePackagingDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $packaging->setName($dto->name);
        $this->em->flush();

        return $this->json([
            'id' => $packaging->getId(),
            'name' => $packaging->getName(),
            'articles_count' => $packaging->getArticles()->count(),
            'created_at' => $packaging->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $packaging->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.packagings.destroy', methods: ['DELETE'])]
    public function destroy(Packaging $packaging): Response
    {
        $this->em->remove($packaging);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}

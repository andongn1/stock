<?php

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Data\Family\CreateFamilyDto;
use App\Data\Family\UpdateFamilyDto;
use App\Entity\Family;
use App\Serializer\FamilyNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/families')]
class FamilyController extends Controller
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api.families.index', methods: ['GET'])]
    public function index(): Response
    {
        $families = $this->em->getRepository(Family::class)->findBy([], ['createdAt' => 'DESC']);
        
        $data = array_map(function(Family $family) {
            return [
                'id' => $family->getId(),
                'name' => $family->getName(),
                'created_at' => $family->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $family->getUpdatedAt()->format('Y-m-d H:i:s'),
                'sub_families_count' => $family->getSubFamilles()->count(),
                'articles_count' => $family->getArticles()->count(),
            ];
        }, $families);

        return $this->json($data);
    }

    #[Route('', name: 'api.families.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $dto = new CreateFamilyDto();
        $this->serializer->deserialize($request->getContent(), CreateFamilyDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $family = new Family();
        $family->setName($dto->name);
        
        $this->em->persist($family);
        $this->em->flush();

        return $this->json([
            'id' => $family->getId(),
            'name' => $family->getName(),
            'created_at' => $family->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $family->getUpdatedAt()->format('Y-m-d H:i:s'),
            'sub_families_count' => 0,
            'articles_count' => 0,
        ], 201);
    }

    #[Route('/{id}', name: 'api.families.show', methods: ['GET'])]
    public function show(Family $family): Response
    {
        return $this->json([
            'id' => $family->getId(),
            'name' => $family->getName(),
            'created_at' => $family->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $family->getUpdatedAt()->format('Y-m-d H:i:s'),
            'sub_families_count' => $family->getSubFamilles()->count(),
            'articles_count' => $family->getArticles()->count(),
        ]);
    }

    #[Route('/{id}', name: 'api.families.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Family $family): Response
    {
        $dto = new UpdateFamilyDto();
        $this->serializer->deserialize($request->getContent(), UpdateFamilyDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $family->setName($dto->name);
        $this->em->flush();

        return $this->json([
            'id' => $family->getId(),
            'name' => $family->getName(),
            'created_at' => $family->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $family->getUpdatedAt()->format('Y-m-d H:i:s'),
            'sub_families_count' => $family->getSubFamilles()->count(),
            'articles_count' => $family->getArticles()->count(),
        ]);
    }

    #[Route('/{id}', name: 'api.families.destroy', methods: ['DELETE'])]
    public function destroy(Family $family): Response
    {
        $this->em->remove($family);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}

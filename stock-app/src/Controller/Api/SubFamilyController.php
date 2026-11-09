<?php

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Data\SubFamily\CreateSubFamilyDto;
use App\Data\SubFamily\UpdateSubFamilyDto;
use App\Entity\Family;
use App\Entity\SubFamily;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/sub-families')]
class SubFamilyController extends Controller
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api.sub_families.index', methods: ['GET'])]
    public function index(): Response
    {
        $subFamilies = $this->em->getRepository(SubFamily::class)->findBy([], ['createdAt' => 'DESC']);
        
        $data = array_map(function(SubFamily $subFamily) {
            return [
                'id' => $subFamily->getId(),
                'name' => $subFamily->getName(),
                'family_id' => $subFamily->getFamily()?->getId(),
                'family' => $subFamily->getFamily() ? [
                    'id' => $subFamily->getFamily()->getId(),
                    'name' => $subFamily->getFamily()->getName(),
                ] : null,
                'articles_count' => $subFamily->getArticles()->count(),
                'created_at' => $subFamily->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $subFamily->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $subFamilies);

        return $this->json($data);
    }

    #[Route('', name: 'api.sub_families.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $dto = new CreateSubFamilyDto();
        $this->serializer->deserialize($request->getContent(), CreateSubFamilyDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $family = $this->em->getRepository(Family::class)->find($dto->family_id);
        
        $subFamily = new SubFamily();
        $subFamily->setName($dto->name);
        $subFamily->setFamily($family);
        
        $this->em->persist($subFamily);
        $this->em->flush();

        return $this->json([
            'id' => $subFamily->getId(),
            'name' => $subFamily->getName(),
            'family_id' => $subFamily->getFamily()?->getId(),
            'family' => [
                'id' => $family->getId(),
                'name' => $family->getName(),
            ],
            'articles_count' => 0,
            'created_at' => $subFamily->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $subFamily->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }

    #[Route('/{id}', name: 'api.sub_families.show', methods: ['GET'])]
    public function show(SubFamily $subFamily): Response
    {
        return $this->json([
            'id' => $subFamily->getId(),
            'name' => $subFamily->getName(),
            'family_id' => $subFamily->getFamily()?->getId(),
            'family' => $subFamily->getFamily() ? [
                'id' => $subFamily->getFamily()->getId(),
                'name' => $subFamily->getFamily()->getName(),
            ] : null,
            'articles_count' => $subFamily->getArticles()->count(),
            'created_at' => $subFamily->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $subFamily->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.sub_families.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, SubFamily $subFamily): Response
    {
        $dto = new UpdateSubFamilyDto();
        $this->serializer->deserialize($request->getContent(), UpdateSubFamilyDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $subFamily->setName($dto->name);
        
        if ($dto->family_id) {
            $family = $this->em->getRepository(Family::class)->find($dto->family_id);
            $subFamily->setFamily($family);
        }
        
        $this->em->flush();

        return $this->json([
            'id' => $subFamily->getId(),
            'name' => $subFamily->getName(),
            'family_id' => $subFamily->getFamily()?->getId(),
            'family' => $subFamily->getFamily() ? [
                'id' => $subFamily->getFamily()->getId(),
                'name' => $subFamily->getFamily()->getName(),
            ] : null,
            'articles_count' => $subFamily->getArticles()->count(),
            'created_at' => $subFamily->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $subFamily->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.sub_families.destroy', methods: ['DELETE'])]
    public function destroy(SubFamily $subFamily): Response
    {
        $this->em->remove($subFamily);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}

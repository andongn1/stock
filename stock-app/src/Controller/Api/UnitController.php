<?php

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Data\Unit\CreateUnitDto;
use App\Data\Unit\UpdateUnitDto;
use App\Entity\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/units')]
class UnitController extends Controller
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api.units.index', methods: ['GET'])]
    public function index(): Response
    {
        $units = $this->em->getRepository(Unit::class)->findBy([], ['createdAt' => 'DESC']);
        
        $data = array_map(function(Unit $unit) {
            return [
                'id' => $unit->getId(),
                'name' => $unit->getName(),
                'articles_count' => $unit->getArticles()->count(),
                'created_at' => $unit->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $unit->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $units);

        return $this->json($data);
    }

    #[Route('', name: 'api.units.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $dto = new CreateUnitDto();
        $this->serializer->deserialize($request->getContent(), CreateUnitDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $unit = new Unit();
        $unit->setName($dto->name);
        
        $this->em->persist($unit);
        $this->em->flush();

        return $this->json([
            'id' => $unit->getId(),
            'name' => $unit->getName(),
            'articles_count' => 0,
            'created_at' => $unit->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $unit->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }

    #[Route('/{id}', name: 'api.units.show', methods: ['GET'])]
    public function show(Unit $unit): Response
    {
        return $this->json([
            'id' => $unit->getId(),
            'name' => $unit->getName(),
            'articles_count' => $unit->getArticles()->count(),
            'created_at' => $unit->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $unit->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.units.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Unit $unit): Response
    {
        $dto = new UpdateUnitDto();
        $this->serializer->deserialize($request->getContent(), UpdateUnitDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $unit->setName($dto->name);
        $this->em->flush();

        return $this->json([
            'id' => $unit->getId(),
            'name' => $unit->getName(),
            'articles_count' => $unit->getArticles()->count(),
            'created_at' => $unit->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $unit->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.units.destroy', methods: ['DELETE'])]
    public function destroy(Unit $unit): Response
    {
        $this->em->remove($unit);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}

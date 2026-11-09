<?php

namespace App\Controller\Api;

use App\Entity\Unit;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/units')]
#[IsGranted('ROLE_USER')]
class UnitController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UnitRepository $unitRepository
    ) {
    }

    #[Route('', name: 'api.units.index', methods: ['GET'])]
    public function index(): Response
    {
        $units = $this->unitRepository->findBy([], ['id' => 'DESC']);

        $data = [];
        foreach ($units as $unit) {
            $data[] = [
                'id' => $unit->getId(),
                'name' => $unit->getName(),
                'created_at' => $unit->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $unit->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('', name: 'api.units.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $unit = new Unit();
        $unit->setName($data['name']);

        $this->entityManager->persist($unit);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $unit->getId(),
            'name' => $unit->getName(),
            'created_at' => $unit->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $unit->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api.units.show', methods: ['GET'])]
    public function show(Unit $unit): Response
    {
        return new JsonResponse([
            'id' => $unit->getId(),
            'name' => $unit->getName(),
            'created_at' => $unit->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $unit->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.units.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Unit $unit): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $unit->setName($data['name']);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $unit->getId(),
            'name' => $unit->getName(),
            'created_at' => $unit->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $unit->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.units.destroy', methods: ['DELETE'])]
    public function destroy(Unit $unit): Response
    {
        $this->entityManager->remove($unit);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

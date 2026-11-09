<?php

namespace App\Controller\Api;

use App\Entity\Packaging;
use App\Repository\PackagingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/packagings')]
#[IsGranted('ROLE_USER')]
class PackagingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PackagingRepository $packagingRepository
    ) {
    }

    #[Route('', name: 'api.packagings.index', methods: ['GET'])]
    public function index(): Response
    {
        $packagings = $this->packagingRepository->findBy([], ['id' => 'DESC']);

        $data = [];
        foreach ($packagings as $packaging) {
            $data[] = [
                'id' => $packaging->getId(),
                'name' => $packaging->getName(),
                'created_at' => $packaging->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $packaging->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('', name: 'api.packagings.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $packaging = new Packaging();
        $packaging->setName($data['name']);

        $this->entityManager->persist($packaging);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $packaging->getId(),
            'name' => $packaging->getName(),
            'created_at' => $packaging->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $packaging->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api.packagings.show', methods: ['GET'])]
    public function show(Packaging $packaging): Response
    {
        return new JsonResponse([
            'id' => $packaging->getId(),
            'name' => $packaging->getName(),
            'created_at' => $packaging->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $packaging->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.packagings.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Packaging $packaging): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $packaging->setName($data['name']);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $packaging->getId(),
            'name' => $packaging->getName(),
            'created_at' => $packaging->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $packaging->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.packagings.destroy', methods: ['DELETE'])]
    public function destroy(Packaging $packaging): Response
    {
        $this->entityManager->remove($packaging);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

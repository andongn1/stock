<?php

namespace App\Controller\Api;

use App\Entity\SubFamily;
use App\Repository\SubFamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/sub-families')]
#[IsGranted('ROLE_USER')]
class SubFamilyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SubFamilyRepository $subFamilyRepository
    ) {
    }

    #[Route('', name: 'api.sub_families.index', methods: ['GET'])]
    public function index(): Response
    {
        $subFamilies = $this->subFamilyRepository->findBy([], ['id' => 'DESC']);

        $data = [];
        foreach ($subFamilies as $subFamily) {
            $data[] = [
                'id' => $subFamily->getId(),
                'name' => $subFamily->getName(),
                'family_id' => $subFamily->getFamily() ? $subFamily->getFamily()->getId() : null,
                'created_at' => $subFamily->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $subFamily->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('', name: 'api.sub_families.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name']) || !isset($data['family_id'])) {
            return new JsonResponse(['error' => 'Name and family_id are required'], Response::HTTP_BAD_REQUEST);
        }

        $family = $this->entityManager->getRepository('App\Entity\Family')->find($data['family_id']);
        if (!$family) {
            return new JsonResponse(['error' => 'Family not found'], Response::HTTP_NOT_FOUND);
        }

        $subFamily = new SubFamily();
        $subFamily->setName($data['name']);
        $subFamily->setFamily($family);

        $this->entityManager->persist($subFamily);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $subFamily->getId(),
            'name' => $subFamily->getName(),
            'family_id' => $family->getId(),
            'created_at' => $subFamily->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $subFamily->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api.sub_families.show', methods: ['GET'])]
    public function show(SubFamily $subFamily): Response
    {
        return new JsonResponse([
            'id' => $subFamily->getId(),
            'name' => $subFamily->getName(),
            'family_id' => $subFamily->getFamily() ? $subFamily->getFamily()->getId() : null,
            'created_at' => $subFamily->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $subFamily->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.sub_families.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, SubFamily $subFamily): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name']) || !isset($data['family_id'])) {
            return new JsonResponse(['error' => 'Name and family_id are required'], Response::HTTP_BAD_REQUEST);
        }

        $family = $this->entityManager->getRepository('App\Entity\Family')->find($data['family_id']);
        if (!$family) {
            return new JsonResponse(['error' => 'Family not found'], Response::HTTP_NOT_FOUND);
        }

        $subFamily->setName($data['name']);
        $subFamily->setFamily($family);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $subFamily->getId(),
            'name' => $subFamily->getName(),
            'family_id' => $family->getId(),
            'created_at' => $subFamily->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $subFamily->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.sub_families.destroy', methods: ['DELETE'])]
    public function destroy(SubFamily $subFamily): Response
    {
        $this->entityManager->remove($subFamily);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

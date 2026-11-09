<?php

namespace App\Controller\Api;

use App\Entity\Family;
use App\Form\FamilyType;
use App\Repository\FamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/families')]
#[IsGranted('ROLE_USER')]
class FamilyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FamilyRepository $familyRepository
    ) {
    }

    #[Route('', name: 'api.families.index', methods: ['GET'])]
    public function index(): Response
    {
        $families = $this->familyRepository->findBy([], ['id' => 'DESC']);

        $data = [];
        foreach ($families as $family) {
            $data[] = [
                'id' => $family->getId(),
                'name' => $family->getName(),
                'created_at' => $family->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $family->getUpdatedAt()->format('Y-m-d H:i:s'),
                'sub_families_count' => count($family->getSubFamilles()),
                'articles_count' => count($family->getArticles()),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('', name: 'api.families.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!is_array($data) || !isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $family = new Family();
        $family->setName($data['name']);

        $this->entityManager->persist($family);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $family->getId(),
            'name' => $family->getName(),
            'created_at' => $family->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $family->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api.families.show', methods: ['GET'])]
    public function show(Family $family): Response
    {
        return new JsonResponse([
            'id' => $family->getId(),
            'name' => $family->getName(),
            'created_at' => $family->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $family->getUpdatedAt()->format('Y-m-d H:i:s'),
            'sub_families_count' => count($family->getSubFamilles()),
            'articles_count' => count($family->getArticles()),
        ]);
    }

    #[Route('/{id}', name: 'api.families.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Family $family): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!is_array($data) || !isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $family->setName($data['name']);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $family->getId(),
            'name' => $family->getName(),
            'created_at' => $family->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $family->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.families.destroy', methods: ['DELETE'])]
    public function destroy(Family $family): Response
    {
        $this->entityManager->remove($family);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

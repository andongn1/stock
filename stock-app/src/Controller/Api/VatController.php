<?php

namespace App\Controller\Api;

use App\Entity\Vat;
use App\Repository\VatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/vats')]
#[IsGranted('ROLE_USER')]
class VatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private VatRepository $vatRepository
    ) {
    }

    #[Route('', name: 'api.vats.index', methods: ['GET'])]
    public function index(): Response
    {
        $vats = $this->vatRepository->findBy([], ['id' => 'DESC']);

        $data = [];
        foreach ($vats as $vat) {
            $data[] = [
                'id' => $vat->getId(),
                'rate' => $vat->getRate(),
                'label' => $vat->getLabel(),
                'created_at' => $vat->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $vat->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('', name: 'api.vats.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['rate']) || !isset($data['label'])) {
            return new JsonResponse(['error' => 'Rate and label are required'], Response::HTTP_BAD_REQUEST);
        }

        $vat = new Vat();
        $vat->setRate($data['rate']);
        $vat->setLabel($data['label']);

        $this->entityManager->persist($vat);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $vat->getId(),
            'rate' => $vat->getRate(),
            'label' => $vat->getLabel(),
            'created_at' => $vat->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $vat->getUpdatedAt()->format('Y-m-d H:i-s'),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api.vats.show', methods: ['GET'])]
    public function show(Vat $vat): Response
    {
        return new JsonResponse([
            'id' => $vat->getId(),
            'rate' => $vat->getRate(),
            'label' => $vat->getLabel(),
            'created_at' => $vat->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $vat->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.vats.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Vat $vat): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['rate']) || !isset($data['label'])) {
            return new JsonResponse(['error' => 'Rate and label are required'], Response::HTTP_BAD_REQUEST);
        }

        $vat->setRate($data['rate']);
        $vat->setLabel($data['label']);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $vat->getId(),
            'rate' => $vat->getRate(),
            'label' => $vat->getLabel(),
            'created_at' => $vat->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $vat->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.vats.destroy', methods: ['DELETE'])]
    public function destroy(Vat $vat): Response
    {
        $this->entityManager->remove($vat);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

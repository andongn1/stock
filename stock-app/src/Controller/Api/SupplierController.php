<?php

namespace App\Controller\Api;

use App\Entity\Supplier;
use App\Repository\SupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/suppliers')]
#[IsGranted('ROLE_USER')]
class SupplierController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SupplierRepository $supplierRepository
    ) {
    }

    #[Route('', name: 'api.suppliers.index', methods: ['GET'])]
    public function index(): Response
    {
        $suppliers = $this->supplierRepository->findBy([], ['id' => 'DESC']);

        $data = [];
        foreach ($suppliers as $supplier) {
            $data[] = [
                'id' => $supplier->getId(),
                'name' => $supplier->getName(),
                'contact_info' => $supplier->getContactInfo(),
                'created_at' => $supplier->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $supplier->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('', name: 'api.suppliers.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $supplier = new Supplier();
        $supplier->setName($data['name']);
        if (isset($data['contact_info'])) {
            $supplier->setContactInfo($data['contact_info']);
        }

        $this->entityManager->persist($supplier);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $supplier->getId(),
            'name' => $supplier->getName(),
            'contact_info' => $supplier->getContactInfo(),
            'created_at' => $supplier->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $supplier->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api.suppliers.show', methods: ['GET'])]
    public function show(Supplier $supplier): Response
    {
        return new JsonResponse([
            'id' => $supplier->getId(),
            'name' => $supplier->getName(),
            'contact_info' => $supplier->getContactInfo(),
            'created_at' => $supplier->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $supplier->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.suppliers.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Supplier $supplier): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $supplier->setName($data['name']);
        if (isset($data['contact_info'])) {
            $supplier->setContactInfo($data['contact_info']);
        }
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $supplier->getId(),
            'name' => $supplier->getName(),
            'contact_info' => $supplier->getContactInfo(),
            'created_at' => $supplier->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $supplier->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.suppliers.destroy', methods: ['DELETE'])]
    public function destroy(Supplier $supplier): Response
    {
        $this->entityManager->remove($supplier);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

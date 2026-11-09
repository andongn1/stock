<?php

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Data\Supplier\CreateSupplierDto;
use App\Data\Supplier\UpdateSupplierDto;
use App\Entity\Supplier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/suppliers')]
class SupplierController extends Controller
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api.suppliers.index', methods: ['GET'])]
    public function index(): Response
    {
        $suppliers = $this->em->getRepository(Supplier::class)->findBy([], ['createdAt' => 'DESC']);
        
        $data = array_map(function(Supplier $supplier) {
            return [
                'id' => $supplier->getId(),
                'name' => $supplier->getName(),
                'contact_info' => $supplier->getContactInfo(),
                'price_histories_count' => $supplier->getPriceHistories()->count(),
                'created_at' => $supplier->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $supplier->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $suppliers);

        return $this->json($data);
    }

    #[Route('', name: 'api.suppliers.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $dto = new CreateSupplierDto();
        $this->serializer->deserialize($request->getContent(), CreateSupplierDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $supplier = new Supplier();
        $supplier->setName($dto->name);
        $supplier->setContactInfo($dto->contact_info);
        
        $this->em->persist($supplier);
        $this->em->flush();

        return $this->json([
            'id' => $supplier->getId(),
            'name' => $supplier->getName(),
            'contact_info' => $supplier->getContactInfo(),
            'price_histories_count' => 0,
            'created_at' => $supplier->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $supplier->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }

    #[Route('/{id}', name: 'api.suppliers.show', methods: ['GET'])]
    public function show(Supplier $supplier): Response
    {
        return $this->json([
            'id' => $supplier->getId(),
            'name' => $supplier->getName(),
            'contact_info' => $supplier->getContactInfo(),
            'price_histories_count' => $supplier->getPriceHistories()->count(),
            'created_at' => $supplier->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $supplier->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.suppliers.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Supplier $supplier): Response
    {
        $dto = new UpdateSupplierDto();
        $this->serializer->deserialize($request->getContent(), UpdateSupplierDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $supplier->setName($dto->name);
        $supplier->setContactInfo($dto->contact_info);
        $this->em->flush();

        return $this->json([
            'id' => $supplier->getId(),
            'name' => $supplier->getName(),
            'contact_info' => $supplier->getContactInfo(),
            'price_histories_count' => $supplier->getPriceHistories()->count(),
            'created_at' => $supplier->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $supplier->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.suppliers.destroy', methods: ['DELETE'])]
    public function destroy(Supplier $supplier): Response
    {
        $this->em->remove($supplier);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}

<?php

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Data\Vat\CreateVatDto;
use App\Data\Vat\UpdateVatDto;
use App\Entity\Vat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/vats')]
class VatController extends Controller
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api.vats.index', methods: ['GET'])]
    public function index(): Response
    {
        $vats = $this->em->getRepository(Vat::class)->findBy([], ['createdAt' => 'DESC']);
        
        $data = array_map(function(Vat $vat) {
            return [
                'id' => $vat->getId(),
                'rate' => $vat->getRate(),
                'label' => $vat->getLabel(),
                'articles_count' => $vat->getArticles()->count(),
                'created_at' => $vat->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $vat->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $vats);

        return $this->json($data);
    }

    #[Route('', name: 'api.vats.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $dto = new CreateVatDto();
        $this->serializer->deserialize($request->getContent(), CreateVatDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $vat = new Vat();
        $vat->setRate($dto->rate);
        $vat->setLabel($dto->label);
        
        $this->em->persist($vat);
        $this->em->flush();

        return $this->json([
            'id' => $vat->getId(),
            'rate' => $vat->getRate(),
            'label' => $vat->getLabel(),
            'articles_count' => 0,
            'created_at' => $vat->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $vat->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }

    #[Route('/{id}', name: 'api.vats.show', methods: ['GET'])]
    public function show(Vat $vat): Response
    {
        return $this->json([
            'id' => $vat->getId(),
            'rate' => $vat->getRate(),
            'label' => $vat->getLabel(),
            'articles_count' => $vat->getArticles()->count(),
            'created_at' => $vat->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $vat->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.vats.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Vat $vat): Response
    {
        $dto = new UpdateVatDto();
        $this->serializer->deserialize($request->getContent(), UpdateVatDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $vat->setRate($dto->rate);
        $vat->setLabel($dto->label);
        $this->em->flush();

        return $this->json([
            'id' => $vat->getId(),
            'rate' => $vat->getRate(),
            'label' => $vat->getLabel(),
            'articles_count' => $vat->getArticles()->count(),
            'created_at' => $vat->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $vat->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api.vats.destroy', methods: ['DELETE'])]
    public function destroy(Vat $vat): Response
    {
        $this->em->remove($vat);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}

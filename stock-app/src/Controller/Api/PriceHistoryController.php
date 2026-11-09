<?php

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Data\PriceHistory\CreatePriceHistoryDto;
use App\Data\PriceHistory\UpdatePriceHistoryDto;
use App\Entity\Article;
use App\Entity\PriceHistory;
use App\Entity\Supplier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/price-histories')]
class PriceHistoryController extends Controller
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api.price_histories.index', methods: ['GET'])]
    public function index(): Response
    {
        $priceHistories = $this->em->getRepository(PriceHistory::class)->findBy([], ['createdAt' => 'DESC']);
        
        $data = array_map(function(PriceHistory $ph) {
            return $this->serializePriceHistory($ph);
        }, $priceHistories);

        return $this->json($data);
    }

    #[Route('', name: 'api.price_histories.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $dto = new CreatePriceHistoryDto();
        $this->serializer->deserialize($request->getContent(), CreatePriceHistoryDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $priceHistory = new PriceHistory();
        $priceHistory->setPrice($dto->price);
        $priceHistory->setCurrency($dto->currency);
        $priceHistory->setUnitPrice($dto->unit_price);
        $priceHistory->setPackagingPrice($dto->packaging_price);
        $priceHistory->setValidFrom(new \DateTime($dto->valid_from));
        $priceHistory->setPriceType($dto->price_type);
        $priceHistory->setPriceTypeLabel($dto->price_type_label);

        if ($dto->valid_to) {
            $priceHistory->setValidTo(new \DateTime($dto->valid_to));
        }

        $article = $this->em->getRepository(Article::class)->find($dto->article_id);
        $supplier = $this->em->getRepository(Supplier::class)->find($dto->supplier_id);

        $priceHistory->setArticle($article);
        $priceHistory->setSupplier($supplier);
        
        $this->em->persist($priceHistory);
        $this->em->flush();

        return $this->json($this->serializePriceHistory($priceHistory), 201);
    }

    #[Route('/{id}', name: 'api.price_histories.show', methods: ['GET'])]
    public function show(PriceHistory $priceHistory): Response
    {
        return $this->json($this->serializePriceHistory($priceHistory));
    }

    #[Route('/{id}', name: 'api.price_histories.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, PriceHistory $priceHistory): Response
    {
        $dto = new UpdatePriceHistoryDto();
        $this->serializer->deserialize($request->getContent(), UpdatePriceHistoryDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $priceHistory->setPrice($dto->price);
        $priceHistory->setCurrency($dto->currency);
        $priceHistory->setUnitPrice($dto->unit_price);
        $priceHistory->setPackagingPrice($dto->packaging_price);
        $priceHistory->setValidFrom(new \DateTime($dto->valid_from));
        $priceHistory->setPriceType($dto->price_type);
        $priceHistory->setPriceTypeLabel($dto->price_type_label);

        if ($dto->valid_to) {
            $priceHistory->setValidTo(new \DateTime($dto->valid_to));
        } else {
            $priceHistory->setValidTo(null);
        }

        if ($dto->article_id) {
            $article = $this->em->getRepository(Article::class)->find($dto->article_id);
            $priceHistory->setArticle($article);
        }
        if ($dto->supplier_id) {
            $supplier = $this->em->getRepository(Supplier::class)->find($dto->supplier_id);
            $priceHistory->setSupplier($supplier);
        }
        
        $this->em->flush();

        return $this->json($this->serializePriceHistory($priceHistory));
    }

    #[Route('/{id}', name: 'api.price_histories.destroy', methods: ['DELETE'])]
    public function destroy(PriceHistory $priceHistory): Response
    {
        $this->em->remove($priceHistory);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }

    #[Route('/current', name: 'api.price_histories.current', methods: ['GET'])]
    public function currentPrices(): Response
    {
        $now = new \DateTime();
        $priceHistories = $this->em->getRepository(PriceHistory::class)
            ->createQueryBuilder('ph')
            ->where('ph.validFrom <= :now')
            ->andWhere('ph.validTo >= :now OR ph.validTo IS NULL')
            ->setParameter('now', $now)
            ->orderBy('ph.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
        
        $data = array_map(function(PriceHistory $ph) {
            return $this->serializePriceHistory($ph);
        }, $priceHistories);

        return $this->json($data);
    }

    #[Route('/articles/{article}', name: 'api.price_histories.by_article', methods: ['GET'])]
    public function byArticle(Article $article): Response
    {
        $priceHistories = $this->em->getRepository(PriceHistory::class)
            ->findBy(['article' => $article], ['createdAt' => 'DESC']);
        
        $data = array_map(function(PriceHistory $ph) {
            return $this->serializePriceHistory($ph, false);
        }, $priceHistories);

        return $this->json($data);
    }

    private function serializePriceHistory(PriceHistory $ph, bool $withArticle = true): array
    {
        $data = [
            'id' => $ph->getId(),
            'article_id' => $ph->getArticle()?->getId(),
            'supplier_id' => $ph->getSupplier()?->getId(),
            'price' => $ph->getPrice(),
            'currency' => $ph->getCurrency(),
            'price_type' => $ph->getPriceType()?->value ?? $ph->getPriceType(),
            'price_type_label' => $ph->getPriceTypeLabel(),
            'unit_price' => $ph->getUnitPrice(),
            'packaging_price' => $ph->getPackagingPrice(),
            'valid_from' => $ph->getValidFrom()->format('Y-m-d'),
            'valid_to' => $ph->getValidTo()?->format('Y-m-d'),
            'created_at' => $ph->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $ph->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        if ($ph->getArticle() && $withArticle) {
            $article = $ph->getArticle();
            $data['article'] = [
                'id' => $article->getId(),
                'name' => $article->getName(),
                'brand' => $article->getBrand(),
                'packaging_unit_quantity' => $article->getPackagingUnitQuantity(),
                'unit' => $article->getUnit() ? [
                    'id' => $article->getUnit()->getId(),
                    'name' => $article->getUnit()->getName(),
                ] : null,
                'packaging' => $article->getPackaging() ? [
                    'id' => $article->getPackaging()->getId(),
                    'name' => $article->getPackaging()->getName(),
                ] : null,
            ];
        }

        if ($ph->getSupplier()) {
            $data['supplier'] = [
                'id' => $ph->getSupplier()->getId(),
                'name' => $ph->getSupplier()->getName(),
            ];
        }

        return $data;
    }
}

<?php

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Data\Article\CreateArticleDto;
use App\Data\Article\UpdateArticleDto;
use App\Entity\Article;
use App\Entity\Family;
use App\Entity\Packaging;
use App\Entity\PriceHistory;
use App\Entity\SubFamily;
use App\Entity\Supplier;
use App\Entity\Unit;
use App\Entity\Vat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/articles')]
class ArticleController extends Controller
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api.articles.index', methods: ['GET'])]
    public function index(): Response
    {
        $articles = $this->em->getRepository(Article::class)->findBy([], ['createdAt' => 'DESC']);
        
        $data = array_map(function(Article $article) {
            return $this->serializeArticle($article, true, false);
        }, $articles);

        return $this->json($data);
    }

    #[Route('', name: 'api.articles.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $dto = new CreateArticleDto();
        $this->serializer->deserialize($request->getContent(), CreateArticleDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $article = new Article();
        $article->setName($dto->name);
        $article->setBrand($dto->brand);
        $article->setPackagingUnitQuantity($dto->packaging_unit_quantity);

        if ($dto->family_id) {
            $article->setFamily($this->em->getRepository(Family::class)->find($dto->family_id));
        }
        if ($dto->sub_family_id) {
            $article->setSubFamily($this->em->getRepository(SubFamily::class)->find($dto->sub_family_id));
        }
        if ($dto->unit_id) {
            $article->setUnit($this->em->getRepository(Unit::class)->find($dto->unit_id));
        }
        if ($dto->packaging_id) {
            $article->setPackaging($this->em->getRepository(Packaging::class)->find($dto->packaging_id));
        }
        if ($dto->vat_id) {
            $article->setVat($this->em->getRepository(Vat::class)->find($dto->vat_id));
        }
        
        $this->em->persist($article);
        $this->em->flush();

        return $this->json($this->serializeArticle($article, true, false), 201);
    }

    #[Route('/{id}', name: 'api.articles.show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->json($this->serializeArticle($article, true, true));
    }

    #[Route('/{id}', name: 'api.articles.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Article $article): Response
    {
        $dto = new UpdateArticleDto();
        $this->serializer->deserialize($request->getContent(), UpdateArticleDto::class, 'json', [
            'object_to_populate' => $dto,
        ]);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $article->setName($dto->name);
        $article->setBrand($dto->brand);
        $article->setPackagingUnitQuantity($dto->packaging_unit_quantity);

        if ($dto->family_id) {
            $article->setFamily($this->em->getRepository(Family::class)->find($dto->family_id));
        }
        if ($dto->sub_family_id) {
            $article->setSubFamily($this->em->getRepository(SubFamily::class)->find($dto->sub_family_id));
        }
        if ($dto->unit_id) {
            $article->setUnit($this->em->getRepository(Unit::class)->find($dto->unit_id));
        }
        if ($dto->packaging_id) {
            $article->setPackaging($this->em->getRepository(Packaging::class)->find($dto->packaging_id));
        }
        if ($dto->vat_id) {
            $article->setVat($this->em->getRepository(Vat::class)->find($dto->vat_id));
        }
        
        $this->em->flush();

        return $this->json($this->serializeArticle($article, true, false));
    }

    #[Route('/{id}', name: 'api.articles.destroy', methods: ['DELETE'])]
    public function destroy(Article $article): Response
    {
        $this->em->remove($article);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }

    #[Route('/search', name: 'api.articles.search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $search = $request->query->get('search', '');
        
        $queryBuilder = $this->em->getRepository(Article::class)->createQueryBuilder('a');
        
        if ($search) {
            $queryBuilder->where('a.name LIKE :search OR a.brand LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        
        $queryBuilder->orderBy('a.createdAt', 'DESC');
        $articles = $queryBuilder->getQuery()->getResult();
        
        $data = array_map(function(Article $article) {
            return $this->serializeArticle($article, true, false);
        }, $articles);

        return $this->json($data);
    }

    private function serializeArticle(Article $article, bool $withRelations, bool $withPriceHistories): array
    {
        $data = [
            'id' => $article->getId(),
            'name' => $article->getName(),
            'brand' => $article->getBrand(),
            'family_id' => $article->getFamily()?->getId(),
            'sub_family_id' => $article->getSubFamily()?->getId(),
            'unit_id' => $article->getUnit()?->getId(),
            'packaging_id' => $article->getPackaging()?->getId(),
            'packaging_unit_quantity' => $article->getPackagingUnitQuantity(),
            'vat_id' => $article->getVat()?->getId(),
            'price_histories_count' => $article->getPriceHistories()->count(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $article->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        if ($withRelations) {
            if ($article->getFamily()) {
                $data['family'] = [
                    'id' => $article->getFamily()->getId(),
                    'name' => $article->getFamily()->getName(),
                ];
            }
            if ($article->getSubFamily()) {
                $data['sub_family'] = [
                    'id' => $article->getSubFamily()->getId(),
                    'name' => $article->getSubFamily()->getName(),
                ];
            }
            if ($article->getUnit()) {
                $data['unit'] = [
                    'id' => $article->getUnit()->getId(),
                    'name' => $article->getUnit()->getName(),
                ];
            }
            if ($article->getPackaging()) {
                $data['packaging'] = [
                    'id' => $article->getPackaging()->getId(),
                    'name' => $article->getPackaging()->getName(),
                ];
            }
            if ($article->getVat()) {
                $data['vat'] = [
                    'id' => $article->getVat()->getId(),
                    'rate' => $article->getVat()->getRate(),
                    'label' => $article->getVat()->getLabel(),
                ];
            }
        }

        // Current price
        $currentPrice = $this->getCurrentPrice($article);
        if ($currentPrice) {
            $data['current_price'] = [
                'price' => $currentPrice->getPrice(),
                'currency' => $currentPrice->getCurrency(),
                'supplier' => $currentPrice->getSupplier()->getName(),
                'valid_from' => $currentPrice->getValidFrom()->format('Y-m-d'),
            ];
        } else {
            $data['current_price'] = null;
        }

        if ($withPriceHistories && $article->getPriceHistories()->count() > 0) {
            $data['price_histories'] = array_map(function(PriceHistory $ph) {
                return [
                    'id' => $ph->getId(),
                    'article_id' => $ph->getArticle()->getId(),
                    'supplier_id' => $ph->getSupplier()->getId(),
                    'price' => $ph->getPrice(),
                    'currency' => $ph->getCurrency(),
                    'price_type' => $ph->getPriceType()?->value ?? $ph->getPriceType(),
                    'price_type_label' => $ph->getPriceTypeLabel(),
                    'unit_price' => $ph->getUnitPrice(),
                    'packaging_price' => $ph->getPackagingPrice(),
                    'valid_from' => $ph->getValidFrom()->format('Y-m-d'),
                    'valid_to' => $ph->getValidTo()?->format('Y-m-d'),
                    'article' => [
                        'id' => $ph->getArticle()->getId(),
                        'name' => $ph->getArticle()->getName(),
                        'brand' => $ph->getArticle()->getBrand(),
                        'packaging_unit_quantity' => $ph->getArticle()->getPackagingUnitQuantity(),
                        'unit' => $ph->getArticle()->getUnit() ? [
                            'id' => $ph->getArticle()->getUnit()->getId(),
                            'name' => $ph->getArticle()->getUnit()->getName(),
                        ] : null,
                        'packaging' => $ph->getArticle()->getPackaging() ? [
                            'id' => $ph->getArticle()->getPackaging()->getId(),
                            'name' => $ph->getArticle()->getPackaging()->getName(),
                        ] : null,
                    ],
                    'supplier' => [
                        'id' => $ph->getSupplier()->getId(),
                        'name' => $ph->getSupplier()->getName(),
                    ],
                    'created_at' => $ph->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $ph->getUpdatedAt()->format('Y-m-d H:i:s'),
                ];
            }, $article->getPriceHistories()->toArray());
        }

        return $data;
    }

    private function getCurrentPrice(Article $article): ?PriceHistory
    {
        $now = new \DateTime();
        foreach ($article->getPriceHistories() as $ph) {
            if ($ph->getValidFrom() <= $now && 
                ($ph->getValidTo() === null || $ph->getValidTo() >= $now)) {
                return $ph;
            }
        }
        return null;
    }
}

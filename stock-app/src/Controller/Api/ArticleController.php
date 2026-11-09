<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\PriceHistory;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/articles')]
#[IsGranted('ROLE_USER')]
class ArticleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ArticleRepository $articleRepository,
        private SerializerInterface $serializer
    ) {
    }

    #[Route('', name: 'api.articles.index', methods: ['GET'])]
    public function index(): Response
    {
        $articles = $this->articleRepository->findBy([], ['id' => 'DESC']);

        $data = [];
        foreach ($articles as $article) {
            $data[] = $this->serializeArticle($article);
        }

        return new JsonResponse($data);
    }

    #[Route('', name: 'api.articles.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        
        $data = json_decode($request->getContent(), true);
        if (is_array($data)) {
            // Manually map data to article
            if (isset($data['name'])) {
                $article->setName($data['name']);
            }
            if (isset($data['brand'])) {
                $article->setBrand($data['brand']);
            }
            if (isset($data['family_id'])) {
                $family = $this->entityManager->getRepository('App\Entity\Family')->find($data['family_id']);
                if ($family) {
                    $article->setFamily($family);
                }
            }
            if (isset($data['sub_family_id'])) {
                $subFamily = $this->entityManager->getRepository('App\Entity\SubFamily')->find($data['sub_family_id']);
                if ($subFamily) {
                    $article->setSubFamily($subFamily);
                }
            }
            if (isset($data['unit_id'])) {
                $unit = $this->entityManager->getRepository('App\Entity\Unit')->find($data['unit_id']);
                if ($unit) {
                    $article->setUnit($unit);
                }
            }
            if (isset($data['packaging_id'])) {
                $packaging = $this->entityManager->getRepository('App\Entity\Packaging')->find($data['packaging_id']);
                if ($packaging) {
                    $article->setPackaging($packaging);
                }
            }
            if (isset($data['packaging_unit_quantity'])) {
                $article->setPackagingUnitQuantity($data['packaging_unit_quantity']);
            }
            if (isset($data['vat_id'])) {
                $vat = $this->entityManager->getRepository('App\Entity\Vat')->find($data['vat_id']);
                if ($vat) {
                    $article->setVat($vat);
                }
            }
            
            $this->entityManager->persist($article);
            $this->entityManager->flush();
            
            return new JsonResponse($this->serializeArticle($article), Response::HTTP_CREATED);
        }

        return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'api.articles.show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return new JsonResponse($this->serializeArticle($article, true));
    }

    #[Route('/{id}', name: 'api.articles.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Article $article): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (is_array($data)) {
            if (isset($data['name'])) {
                $article->setName($data['name']);
            }
            if (isset($data['brand'])) {
                $article->setBrand($data['brand']);
            }
            if (isset($data['family_id'])) {
                $family = $this->entityManager->getRepository('App\Entity\Family')->find($data['family_id']);
                if ($family) {
                    $article->setFamily($family);
                }
            }
            if (isset($data['sub_family_id'])) {
                $subFamily = $this->entityManager->getRepository('App\Entity\SubFamily')->find($data['sub_family_id']);
                if ($subFamily) {
                    $article->setSubFamily($subFamily);
                }
            }
            if (isset($data['unit_id'])) {
                $unit = $this->entityManager->getRepository('App\Entity\Unit')->find($data['unit_id']);
                if ($unit) {
                    $article->setUnit($unit);
                }
            }
            if (isset($data['packaging_id'])) {
                $packaging = $this->entityManager->getRepository('App\Entity\Packaging')->find($data['packaging_id']);
                if ($packaging) {
                    $article->setPackaging($packaging);
                }
            }
            if (isset($data['packaging_unit_quantity'])) {
                $article->setPackagingUnitQuantity($data['packaging_unit_quantity']);
            }
            if (isset($data['vat_id'])) {
                $vat = $this->entityManager->getRepository('App\Entity\Vat')->find($data['vat_id']);
                if ($vat) {
                    $article->setVat($vat);
                }
            }
            
            $this->entityManager->flush();
            
            return new JsonResponse($this->serializeArticle($article));
        }

        return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'api.articles.destroy', methods: ['DELETE'])]
    public function destroy(Article $article): Response
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/search', name: 'api.articles.search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $search = $request->query->get('search', '');
        
        $queryBuilder = $this->articleRepository->createQueryBuilder('a');
        $queryBuilder
            ->leftJoin('a.family', 'f')
            ->leftJoin('a.subFamily', 'sf')
            ->leftJoin('a.unit', 'u')
            ->leftJoin('a.packaging', 'p')
            ->leftJoin('a.vat', 'v')
            ->leftJoin('a.priceHistories', 'ph')
            ->orderBy('a.id', 'DESC');

        if (!empty($search)) {
            $queryBuilder
                ->where('a.name LIKE :search OR a.brand LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $articles = $queryBuilder->getQuery()->getResult();

        $data = [];
        foreach ($articles as $article) {
            $data[] = $this->serializeArticle($article);
        }

        return new JsonResponse($data);
    }

    private function serializeArticle(Article $article, bool $withRelations = false): array
    {
        $data = [
            'id' => $article->getId(),
            'name' => $article->getName(),
            'brand' => $article->getBrand(),
            'family_id' => $article->getFamily() ? $article->getFamily()->getId() : null,
            'sub_family_id' => $article->getSubFamily() ? $article->getSubFamily()->getId() : null,
            'unit_id' => $article->getUnit() ? $article->getUnit()->getId() : null,
            'packaging_id' => $article->getPackaging() ? $article->getPackaging()->getId() : null,
            'packaging_unit_quantity' => $article->getPackagingUnitQuantity(),
            'vat_id' => $article->getVat() ? $article->getVat()->getId() : null,
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $article->getUpdatedAt()->format('Y-m-d H:i:s'),
            'price_histories_count' => count($article->getPriceHistories()),
        ];

        if ($withRelations) {
            $data['family'] = $article->getFamily() ? [
                'id' => $article->getFamily()->getId(),
                'name' => $article->getFamily()->getName(),
            ] : null;

            $data['sub_family'] = $article->getSubFamily() ? [
                'id' => $article->getSubFamily()->getId(),
                'name' => $article->getSubFamily()->getName(),
            ] : null;

            $data['unit'] = $article->getUnit() ? [
                'id' => $article->getUnit()->getId(),
                'name' => $article->getUnit()->getName(),
            ] : null;

            $data['packaging'] = $article->getPackaging() ? [
                'id' => $article->getPackaging()->getId(),
                'name' => $article->getPackaging()->getName(),
            ] : null;

            $data['vat'] = $article->getVat() ? [
                'id' => $article->getVat()->getId(),
                'rate' => $article->getVat()->getRate(),
                'label' => $article->getVat()->getLabel(),
            ] : null;

            $currentPrice = $article->getCurrentPrice();
            if ($currentPrice) {
                $data['current_price'] = [
                    'price' => $currentPrice->getPrice(),
                    'currency' => $currentPrice->getCurrency(),
                    'supplier' => $currentPrice->getSupplier() ? $currentPrice->getSupplier()->getName() : null,
                    'valid_from' => $currentPrice->getValidFrom() ? $currentPrice->getValidFrom()->format('Y-m-d') : null,
                ];
            } else {
                $data['current_price'] = null;
            }

            $data['price_histories'] = [];
            foreach ($article->getPriceHistories() as $priceHistory) {
                $data['price_histories'][] = [
                    'id' => $priceHistory->getId(),
                    'article_id' => $priceHistory->getArticle()->getId(),
                    'supplier_id' => $priceHistory->getSupplier() ? $priceHistory->getSupplier()->getId() : null,
                    'price' => $priceHistory->getPrice(),
                    'currency' => $priceHistory->getCurrency(),
                    'price_type' => $priceHistory->getPriceType(),
                    'valid_from' => $priceHistory->getValidFrom() ? $priceHistory->getValidFrom()->format('Y-m-d') : null,
                    'valid_to' => $priceHistory->getValidTo() ? $priceHistory->getValidTo()->format('Y-m-d') : null,
                    'supplier' => $priceHistory->getSupplier() ? [
                        'id' => $priceHistory->getSupplier()->getId(),
                        'name' => $priceHistory->getSupplier()->getName(),
                    ] : null,
                ];
            }
        }

        return $data;
    }
}

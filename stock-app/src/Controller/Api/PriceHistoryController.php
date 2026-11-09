<?php

namespace App\Controller\Api;

use App\Entity\PriceHistory;
use App\Repository\PriceHistoryRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/price-histories')]
#[IsGranted('ROLE_USER')]
class PriceHistoryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PriceHistoryRepository $priceHistoryRepository,
        private ArticleRepository $articleRepository
    ) {
    }

    #[Route('', name: 'api.price_histories.index', methods: ['GET'])]
    public function index(): Response
    {
        $priceHistories = $this->priceHistoryRepository->findBy([], ['validFrom' => 'DESC', 'id' => 'DESC']);

        $data = [];
        foreach ($priceHistories as $priceHistory) {
            $data[] = $this->serializePriceHistory($priceHistory);
        }

        return new JsonResponse($data);
    }

    #[Route('', name: 'api.price_histories.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['article_id']) || !isset($data['supplier_id']) || !isset($data['price'])) {
            return new JsonResponse(['error' => 'article_id, supplier_id, and price are required'], Response::HTTP_BAD_REQUEST);
        }

        $article = $this->articleRepository->find($data['article_id']);
        if (!$article) {
            return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        $supplier = $this->entityManager->getRepository('App\Entity\Supplier')->find($data['supplier_id']);
        if (!$supplier) {
            return new JsonResponse(['error' => 'Supplier not found'], Response::HTTP_NOT_FOUND);
        }

        $priceHistory = new PriceHistory();
        $priceHistory->setArticle($article);
        $priceHistory->setSupplier($supplier);
        $priceHistory->setPrice($data['price']);
        $priceHistory->setCurrency($data['currency'] ?? 'EUR');
        $priceHistory->setPriceType($data['price_type'] ?? 'per_unit');
        if (isset($data['valid_from'])) {
            $priceHistory->setValidFrom(new \DateTime($data['valid_from']));
        } else {
            $priceHistory->setValidFrom(new \DateTime());
        }
        if (isset($data['valid_to'])) {
            $priceHistory->setValidTo(new \DateTime($data['valid_to']));
        }

        $this->entityManager->persist($priceHistory);
        $this->entityManager->flush();

        return new JsonResponse($this->serializePriceHistory($priceHistory), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api.price_histories.show', methods: ['GET'])]
    public function show(PriceHistory $priceHistory): Response
    {
        return new JsonResponse($this->serializePriceHistory($priceHistory));
    }

    #[Route('/{id}', name: 'api.price_histories.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, PriceHistory $priceHistory): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['price'])) {
            $priceHistory->setPrice($data['price']);
        }
        if (isset($data['currency'])) {
            $priceHistory->setCurrency($data['currency']);
        }
        if (isset($data['price_type'])) {
            $priceHistory->setPriceType($data['price_type']);
        }
        if (isset($data['valid_from'])) {
            $priceHistory->setValidFrom(new \DateTime($data['valid_from']));
        }
        if (isset($data['valid_to'])) {
            $priceHistory->setValidTo(new \DateTime($data['valid_to']));
        }

        $this->entityManager->flush();

        return new JsonResponse($this->serializePriceHistory($priceHistory));
    }

    #[Route('/{id}', name: 'api.price_histories.destroy', methods: ['DELETE'])]
    public function destroy(PriceHistory $priceHistory): Response
    {
        $this->entityManager->remove($priceHistory);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/current', name: 'api.price_histories.current', methods: ['GET'])]
    public function currentPrices(): Response
    {
        // Get all articles with their current price (most recent)
        $articles = $this->articleRepository->findAll();

        $data = [];
        foreach ($articles as $article) {
            $currentPrice = $article->getCurrentPrice();
            if ($currentPrice) {
                $data[] = $this->serializePriceHistory($currentPrice);
            }
        }

        return new JsonResponse($data);
    }

    #[Route('/article/{articleId}', name: 'api.price_histories.by_article', methods: ['GET'])]
    public function byArticle(int $articleId): Response
    {
        $article = $this->articleRepository->find($articleId);
        if (!$article) {
            return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        $priceHistories = $this->priceHistoryRepository->findBy(
            ['article' => $article],
            ['validFrom' => 'DESC', 'id' => 'DESC']
        );

        $data = [];
        foreach ($priceHistories as $priceHistory) {
            $data[] = $this->serializePriceHistory($priceHistory);
        }

        return new JsonResponse($data);
    }

    private function serializePriceHistory(PriceHistory $priceHistory): array
    {
        return [
            'id' => $priceHistory->getId(),
            'article_id' => $priceHistory->getArticle()->getId(),
            'supplier_id' => $priceHistory->getSupplier() ? $priceHistory->getSupplier()->getId() : null,
            'price' => $priceHistory->getPrice(),
            'currency' => $priceHistory->getCurrency(),
            'price_type' => $priceHistory->getPriceType(),
            'valid_from' => $priceHistory->getValidFrom() ? $priceHistory->getValidFrom()->format('Y-m-d') : null,
            'valid_to' => $priceHistory->getValidTo() ? $priceHistory->getValidTo()->format('Y-m-d') : null,
            'created_at' => $priceHistory->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $priceHistory->getUpdatedAt()->format('Y-m-d H:i:s'),
            'supplier' => $priceHistory->getSupplier() ? [
                'id' => $priceHistory->getSupplier()->getId(),
                'name' => $priceHistory->getSupplier()->getName(),
            ] : null,
        ];
    }
}

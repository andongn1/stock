<?php

namespace App\Controller\Web;

use App\Controller\Controller;
use App\Entity\Article;
use App\Entity\Family;
use App\Entity\Packaging;
use App\Entity\PriceHistory;
use App\Entity\SellingPrice;
use App\Entity\SubFamily;
use App\Entity\Supplier;
use App\Entity\Unit;
use App\Entity\Vat;
use App\Form\ArticleType;
use App\Form\PriceHistoryType;
use App\Form\SellingPriceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
    #[Route('/articles', name: 'articles.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository(Article::class)->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/create', name: 'articles.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        return $this->render('articles/create.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/articles/{id}', name: 'articles.show')]
    public function show(Article $article, EntityManagerInterface $em): Response
    {
        $suppliers = $em->getRepository(Supplier::class)->findAll();
        $allPrices = $em->getRepository(PriceHistory::class)->findBy(['article' => $article], ['validFrom' => 'DESC', 'id' => 'DESC']);
        
        $pricesBySupplier = [];
        foreach ($allPrices as $price) {
            if (!isset($pricesBySupplier[$price->getSupplier()->getId()])) {
                $pricesBySupplier[$price->getSupplier()->getId()] = $price;
            }
        }
        
        $currentPrice = $allPrices[0] ?? null;
        $otherSupplierPrices = array_filter($pricesBySupplier, function($price) use ($currentPrice) {
            return $price->getId() !== ($currentPrice?->getId() ?? null);
        });

        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'suppliers' => $suppliers,
            'otherSupplierPrices' => $otherSupplierPrices,
            'currentPrice' => $currentPrice,
            'pricesBySupplier' => $pricesBySupplier,
        ]);
    }

    #[Route('/articles/{id}/edit', name: 'articles.edit')]
    public function edit(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        return $this->render('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/articles/{id}', name: 'articles.destroy', methods: ['DELETE'])]
    public function destroy(Article $article, EntityManagerInterface $em): Response
    {
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('articles.index');
    }

    #[Route('/articles/{id}/add-price', name: 'articles.add_price', methods: ['POST'])]
    public function addPrice(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $supplierId = $request->request->get('supplier_id');
        $price = $request->request->get('price');
        $currency = $request->request->get('currency', 'EUR');
        $priceType = $request->request->get('price_type', 'per_unit');

        if (!$supplierId || !$price) {
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $supplier = $em->getRepository(Supplier::class)->find($supplierId);
        if (!$supplier) {
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $priceHistory = new PriceHistory();
        $priceHistory->setArticle($article);
        $priceHistory->setSupplier($supplier);
        $priceHistory->setPrice($price);
        $priceHistory->setCurrency($currency);
        $priceHistory->setPriceType($priceType);
        $priceHistory->setValidFrom(new \DateTime());
        $priceHistory->setValidTo(null);

        $em->persist($priceHistory);
        $em->flush();

        return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
    }

    #[Route('/articles/{id}/price-histories', name: 'articles.price_histories')]
    public function priceHistories(Article $article, EntityManagerInterface $em): Response
    {
        $priceHistories = $em->getRepository(PriceHistory::class)->findBy(['article' => $article], ['validFrom' => 'DESC', 'id' => 'DESC']);

        return $this->render('articles/price_histories.html.twig', [
            'article' => $article,
            'priceHistories' => $priceHistories,
        ]);
    }

    #[Route('/articles/{id}/set-current-supplier', name: 'articles.set_current_supplier', methods: ['POST'])]
    public function setCurrentSupplier(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $priceHistoryId = $request->request->get('price_history_id');
        
        if (!$priceHistoryId) {
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $priceHistory = $em->getRepository(PriceHistory::class)->find($priceHistoryId);
        
        if (!$priceHistory || $priceHistory->getArticle()->getId() !== $article->getId()) {
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        // Create a new price history with today's date for this supplier
        $newPrice = new PriceHistory();
        $newPrice->setArticle($article);
        $newPrice->setSupplier($priceHistory->getSupplier());
        $newPrice->setPrice($priceHistory->getPrice());
        $newPrice->setCurrency($priceHistory->getCurrency());
        $newPrice->setPriceType($priceHistory->getPriceType());
        $newPrice->setValidFrom(new \DateTime());
        $newPrice->setValidTo(null);

        $em->persist($newPrice);
        $em->flush();

        return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
    }

    #[Route('/articles/{id}/update-selling-price', name: 'articles.update_selling_price', methods: ['POST'])]
    public function updateSellingPrice(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $priceTtc = $request->request->get('price_ttc');
        $saleType = $request->request->get('sale_type', 'per_unit');
        $quantity = $request->request->get('quantity', 1);
        $currency = $request->request->get('currency', 'EUR');

        if (!$priceTtc) {
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $sellingPrice = $article->getSellingPrices()->first();
        
        if ($sellingPrice) {
            $sellingPrice->setPriceTtc($priceTtc);
            $sellingPrice->setSaleType($saleType);
            $sellingPrice->setQuantity($quantity);
            $sellingPrice->setCurrency($currency);
        } else {
            $sellingPrice = new SellingPrice();
            $sellingPrice->setArticle($article);
            $sellingPrice->setPriceTtc($priceTtc);
            $sellingPrice->setSaleType($saleType);
            $sellingPrice->setQuantity($quantity);
            $sellingPrice->setCurrency($currency);
            $em->persist($sellingPrice);
        }

        $em->flush();

        return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
    }
}

<?php

namespace App\Controller\Web;

use App\Entity\Article;
use App\Entity\PriceHistory;
use App\Entity\SellingPrice;
use App\Entity\Supplier;
use App\Enum\PriceType;
use App\Enum\SaleType;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\FamilyRepository;
use App\Repository\PackagingRepository;
use App\Repository\PriceHistoryRepository;
use App\Repository\SubFamilyRepository;
use App\Repository\SupplierRepository;
use App\Repository\UnitRepository;
use App\Repository\VatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ArticleRepository $articleRepository,
        private FamilyRepository $familyRepository,
        private SubFamilyRepository $subFamilyRepository,
        private UnitRepository $unitRepository,
        private PackagingRepository $packagingRepository,
        private VatRepository $vatRepository,
        private SupplierRepository $supplierRepository,
        private PriceHistoryRepository $priceHistoryRepository,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/', name: 'articles.index', methods: ['GET'])]
    public function index(): Response
    {
        $articles = $this->articleRepository->findBy([], ['id' => 'DESC']);

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/create', name: 'articles.create', methods: ['GET'])]
    public function create(): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $families = $this->familyRepository->findBy([], ['name' => 'ASC']);
        $subFamilles = $this->subFamilyRepository->findBy([], ['name' => 'ASC']);
        $units = $this->unitRepository->findBy([], ['name' => 'ASC']);
        $packagings = $this->packagingRepository->findBy([], ['name' => 'ASC']);
        $vats = $this->vatRepository->findBy([], ['label' => 'ASC']);

        return $this->render('articles/create.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'families' => $families,
            'subFamilles' => $subFamilles,
            'units' => $units,
            'packagings' => $packagings,
            'vats' => $vats,
        ]);
    }

    #[Route('/', name: 'articles.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->addFlash('success', 'Article cree avec succes !');

            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $families = $this->familyRepository->findBy([], ['name' => 'ASC']);
        $subFamilles = $this->subFamilyRepository->findBy([], ['name' => 'ASC']);
        $units = $this->unitRepository->findBy([], ['name' => 'ASC']);
        $packagings = $this->packagingRepository->findBy([], ['name' => 'ASC']);
        $vats = $this->vatRepository->findBy([], ['label' => 'ASC']);

        return $this->render('articles/create.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'families' => $families,
            'subFamilles' => $subFamilles,
            'units' => $units,
            'packagings' => $packagings,
            'vats' => $vats,
        ]);
    }

    #[Route('/{id}/edit', name: 'articles.edit', methods: ['GET'])]
    public function edit(Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);

        $families = $this->familyRepository->findBy([], ['name' => 'ASC']);
        $subFamilles = $this->subFamilyRepository->findBy([], ['name' => 'ASC']);
        $units = $this->unitRepository->findBy([], ['name' => 'ASC']);
        $packagings = $this->packagingRepository->findBy([], ['name' => 'ASC']);
        $vats = $this->vatRepository->findBy([], ['label' => 'ASC']);

        return $this->render('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'families' => $families,
            'subFamilles' => $subFamilles,
            'units' => $units,
            'packagings' => $packagings,
            'vats' => $vats,
        ]);
    }

    #[Route('/{id}', name: 'articles.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Article mis a jour avec succes !');

            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $families = $this->familyRepository->findBy([], ['name' => 'ASC']);
        $subFamilles = $this->subFamilyRepository->findBy([], ['name' => 'ASC']);
        $units = $this->unitRepository->findBy([], ['name' => 'ASC']);
        $packagings = $this->packagingRepository->findBy([], ['name' => 'ASC']);
        $vats = $this->vatRepository->findBy([], ['label' => 'ASC']);

        return $this->render('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'families' => $families,
            'subFamilles' => $subFamilles,
            'units' => $units,
            'packagings' => $packagings,
            'vats' => $vats,
        ]);
    }

    #[Route('/{id}', name: 'articles.destroy', methods: ['DELETE'])]
    public function destroy(Article $article): RedirectResponse
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        $this->addFlash('success', 'Article supprime avec succes !');

        return $this->redirectToRoute('articles.index');
    }

    #[Route('/{id}', name: 'articles.show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        $suppliers = $this->supplierRepository->findBy([], ['name' => 'ASC']);

        $allPrices = $this->priceHistoryRepository->findBy(
            ['article' => $article],
            ['validFrom' => 'DESC', 'id' => 'DESC']
        );

        $pricesBySupplier = [];
        $supplierIds = [];
        foreach ($allPrices as $price) {
            $supplierId = $price->getSupplier() ? $price->getSupplier()->getId() : null;
            if ($supplierId && !isset($pricesBySupplier[$supplierId])) {
                $pricesBySupplier[$supplierId] = $price;
                $supplierIds[] = $supplierId;
            }
        }

        $currentPrice = !empty($allPrices) ? $allPrices[0] : null;

        $otherSupplierPrices = [];
        if ($currentPrice && $currentPrice->getSupplier()) {
            $currentSupplierId = $currentPrice->getSupplier()->getId();
            $otherSupplierPrices = array_filter($pricesBySupplier, function ($price) use ($currentSupplierId) {
                return $price->getSupplier() && $price->getSupplier()->getId() !== $currentSupplierId;
            });
        }

        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'suppliers' => $suppliers,
            'otherSupplierPrices' => $otherSupplierPrices,
            'currentPrice' => $currentPrice,
            'pricesBySupplier' => $pricesBySupplier,
        ]);
    }

    #[Route('/{id}/add-price', name: 'articles.add-price', methods: ['POST'])]
    public function addPrice(Request $request, Article $article): RedirectResponse
    {
        $supplierId = $request->request->get('supplier_id');
        $price = $request->request->get('price');
        $currency = $request->request->get('currency', 'EUR');
        $priceType = $request->request->get('price_type', 'per_unit');

        $errors = [];
        
        if (!$supplierId) {
            $errors[] = 'Le fournisseur est obligatoire.';
        }
        if ($price === null || $price === '') {
            $errors[] = 'Le prix est obligatoire.';
        }
        if ($price !== null && ($price < 0 || !is_numeric($price))) {
            $errors[] = 'Le prix doit etre un nombre positif.';
        }
        if (!$currency || strlen($currency) !== 3) {
            $errors[] = 'La devise doit comporter 3 caracteres.';
        }
        if (!in_array($priceType, ['per_unit', 'per_packaging'])) {
            $errors[] = 'Le type de prix doit etre soit "par unite" soit "par conditionnement".';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $supplier = $this->supplierRepository->find($supplierId);
        if (!$supplier) {
            $this->addFlash('error', 'Fournisseur introuvable.');
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $priceHistory = new PriceHistory();
        $priceHistory->setArticle($article);
        $priceHistory->setSupplier($supplier);
        $priceHistory->setPrice($price);
        $priceHistory->setCurrency($currency);
        $priceHistory->setPriceType($priceType);
        $priceHistory->setValidFrom(new \DateTime());

        $this->entityManager->persist($priceHistory);
        $this->entityManager->flush();

        $this->addFlash('success', 'Prix ajoute avec succes pour ' . $supplier->getName());

        return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
    }

    #[Route('/{id}/price-histories', name: 'articles.price-histories', methods: ['GET'])]
    public function priceHistories(Article $article): Response
    {
        $priceHistories = $this->priceHistoryRepository->findBy(
            ['article' => $article],
            ['validFrom' => 'DESC', 'id' => 'DESC']
        );

        return $this->render('articles/price-histories.html.twig', [
            'article' => $article,
            'priceHistories' => $priceHistories,
        ]);
    }

    #[Route('/{id}/set-current-supplier', name: 'articles.set-current-supplier', methods: ['POST'])]
    public function setCurrentSupplier(Request $request, Article $article): RedirectResponse
    {
        $priceHistoryId = $request->request->get('price_history_id');

        if (!$priceHistoryId) {
            $this->addFlash('error', 'Aucun prix selectionne.');
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $priceHistory = $this->priceHistoryRepository->find($priceHistoryId);

        if (!$priceHistory) {
            $this->addFlash('error', 'Prix introuvable.');
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        if ($priceHistory->getArticle()->getId() !== $article->getId()) {
            $this->addFlash('error', 'Le prix selectionne n\'appartient pas a cet article.');
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $supplier = $priceHistory->getSupplier();
        if (!$supplier) {
            $this->addFlash('error', 'Fournisseur introuvable pour ce prix.');
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        $newPrice = new PriceHistory();
        $newPrice->setArticle($article);
        $newPrice->setSupplier($supplier);
        $newPrice->setPrice($priceHistory->getPrice());
        $newPrice->setCurrency($priceHistory->getCurrency());
        $newPrice->setPriceType($priceHistory->getPriceType());
        $newPrice->setValidFrom(new \DateTime());

        $this->entityManager->persist($newPrice);
        $this->entityManager->flush();

        $this->addFlash('success', 'Fournisseur active avec succes ! Le prix de ' . $supplier->getName() . ' est maintenant le prix actuel.');

        return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
    }

    #[Route('/{id}/update-selling-price', name: 'articles.update-selling-price', methods: ['POST'])]
    public function updateSellingPrice(Request $request, Article $article): RedirectResponse
    {
        $priceTtc = $request->request->get('price_ttc');
        $saleType = $request->request->get('sale_type', 'per_unit');
        $quantity = $request->request->get('quantity');
        $currency = $request->request->get('currency', 'EUR');

        $errors = [];
        
        if ($priceTtc === null || $priceTtc === '') {
            $errors[] = 'Le prix TTC est obligatoire.';
        }
        if ($priceTtc !== null && ($priceTtc < 0 || !is_numeric($priceTtc))) {
            $errors[] = 'Le prix TTC doit etre un nombre superieur ou egal a 0.';
        }
        if (!$saleType || !in_array($saleType, ['per_unit', 'per_packaging'])) {
            $errors[] = 'Le type de vente est obligatoire.';
        }
        if ($quantity !== null && ($quantity < 0 || !is_numeric($quantity))) {
            $errors[] = 'La quantite doit etre un nombre superieur ou egal a 0.';
        }
        if (!$currency || strlen($currency) !== 3) {
            $errors[] = 'La devise doit comporter 3 caracteres.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
            return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
        }

        if ($quantity === null || $quantity === '') {
            $quantity = ($saleType === 'per_unit') ? 1 : 1;
        }

        $sellingPrice = $article->getSellingPrices()->first() ?: null;

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
            $this->entityManager->persist($sellingPrice);
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Prix de vente mis a jour avec succes !');

        return $this->redirectToRoute('articles.show', ['id' => $article->getId()]);
    }
}

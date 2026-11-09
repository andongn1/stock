<?php

namespace App\Controller\Web;

use App\Repository\ArticleRepository;
use App\Repository\EntryRepository;
use App\Repository\FamilyRepository;
use App\Repository\InventoryRepository;
use App\Repository\SupplierRepository;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ArticleRepository $articleRepository,
        private FamilyRepository $familyRepository,
        private SupplierRepository $supplierRepository,
        private UnitRepository $unitRepository,
        private EntryRepository $entryRepository,
        private InventoryRepository $inventoryRepository
    ) {
    }

    #[Route('/', name: 'app_root')]
    public function root(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }
        return $this->redirectToRoute('login');
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        $articleCount = $this->articleRepository->count([]);
        $familyCount = $this->familyRepository->count([]);
        $supplierCount = $this->supplierRepository->count([]);
        $unitCount = $this->unitRepository->count([]);
        $entryCount = $this->entryRepository->count([]);
        $inventoryCount = $this->inventoryRepository->count([]);

        // Articles per family (top 5)
        $articlesPerFamily = $this->entityManager->createQuery(
            "SELECT f.name, COUNT(a.id) as count FROM App\Entity\Family f LEFT JOIN f.articles a GROUP BY f.id ORDER BY count DESC"
        )->setMaxResults(5)->getResult();

        $maxCount = !empty($articlesPerFamily) ? max(array_column($articlesPerFamily, 'count')) : 1;

        // Articles with/without prices
        $articlesWithPrices = $this->entityManager->createQuery(
            "SELECT COUNT(DISTINCT a.id) FROM App\Entity\Article a JOIN a.priceHistories ph"
        )->getSingleScalarResult();
        $articlesWithoutPrices = $articleCount - $articlesWithPrices;

        // Recent entries (last 5)
        $recentEntries = $this->entryRepository->findBy([], ['createdAt' => 'DESC'], 5);

        // Recent inventories (last 5)
        $recentInventories = $this->inventoryRepository->findBy([], ['createdAt' => 'DESC'], 5);

        return $this->render('dashboard.html.twig', [
            'articleCount' => $articleCount,
            'familyCount' => $familyCount,
            'supplierCount' => $supplierCount,
            'unitCount' => $unitCount,
            'entryCount' => $entryCount,
            'inventoryCount' => $inventoryCount,
            'articlesPerFamily' => $articlesPerFamily,
            'maxCount' => $maxCount,
            'articlesWithPrices' => $articlesWithPrices,
            'articlesWithoutPrices' => $articlesWithoutPrices,
            'recentEntries' => $recentEntries,
            'recentInventories' => $recentInventories,
        ]);
    }
}

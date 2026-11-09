<?php

namespace App\Controller\Web;

use App\Entity\Article;
use App\Entity\Inventory;
use App\Form\InventoryType;
use App\Repository\ArticleRepository;
use App\Repository\FamilyRepository;
use App\Repository\InventoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inventory')]
class InventoryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private InventoryRepository $inventoryRepository,
        private ArticleRepository $articleRepository,
        private FamilyRepository $familyRepository
    ) {
    }

    #[Route('/', name: 'inventories.index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->search($request);
    }

    #[Route('/search', name: 'inventories.search', methods: ['GET', 'POST'])]
    public function search(Request $request): Response
    {
        $search = $request->query->get('search', '');
        $familyId = $request->query->get('family_id');

        $queryBuilder = $this->articleRepository->createQueryBuilder('a');
        $queryBuilder
            ->leftJoin('a.priceHistories', 'ph')
            ->leftJoin('a.packaging', 'p')
            ->leftJoin('a.unit', 'u')
            ->leftJoin('a.family', 'f')
            ->orderBy('a.name', 'ASC');

        if ($familyId) {
            $queryBuilder->andWhere('a.family = :familyId')
                ->setParameter('familyId', $familyId);
        }

        if (!empty($search)) {
            $queryBuilder->andWhere('a.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $articles = $queryBuilder->getQuery()->getResult();

        $todayInventories = $this->getTodayInventories();
        $families = $this->familyRepository->findBy([], ['name' => 'ASC']);

        [$todayStart, $tomorrowStart] = $this->getTodayRange();

        $previousStocks = [];
        $todayStocks = [];

        foreach ($articles as $article) {
            $lastInventory = $this->inventoryRepository->createQueryBuilder('i')
                ->where('i.article = :article')
                ->andWhere('i.date < :todayStart')
                ->setParameter('article', $article)
                ->setParameter('todayStart', $todayStart)
                ->orderBy('i.date', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            $previousStocks[$article->getId()] = $lastInventory ? $lastInventory->getQuantity() : 0;

            $todayInventory = $this->inventoryRepository->createQueryBuilder('i')
                ->where('i.article = :article')
                ->andWhere('i.date >= :todayStart')
                ->andWhere('i.date < :tomorrowStart')
                ->setParameter('article', $article)
                ->setParameter('todayStart', $todayStart)
                ->setParameter('tomorrowStart', $tomorrowStart)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            $todayStocks[$article->getId()] = $todayInventory ? $todayInventory->getQuantity() : null;
        }

        return $this->render('inventories/index.html.twig', [
            'articles' => $articles,
            'search' => $search,
            'todayInventories' => $todayInventories,
            'families' => $families,
            'familyId' => $familyId,
            'previousStocks' => $previousStocks,
            'todayStocks' => $todayStocks,
        ]);
    }

    #[Route('/article/{id}', name: 'inventories.create', methods: ['GET'])]
    public function create(Article $article): Response
    {
        $form = $this->createForm(InventoryType::class, new Inventory());

        return $this->render('inventories/create.html.twig', [
            'article' => $article,
            'todayInventories' => $this->getTodayInventories(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}', name: 'inventories.store', methods: ['POST'])]
    public function store(Request $request, Article $article): RedirectResponse
    {
        $inventory = new Inventory();
        $form = $this->createForm(InventoryType::class, $inventory);
        $form->handleRequest($request);

        $familyId = $request->query->get('family_id');

        if ($form->isSubmitted() && $form->isValid()) {
            [$todayStart, $tomorrowStart] = $this->getTodayRange();

            $existing = $this->inventoryRepository->createQueryBuilder('i')
                ->where('i.article = :article')
                ->andWhere('i.date >= :todayStart')
                ->andWhere('i.date < :tomorrowStart')
                ->setParameter('article', $article)
                ->setParameter('todayStart', $todayStart)
                ->setParameter('tomorrowStart', $tomorrowStart)
                ->getQuery()
                ->getOneOrNullResult();

            if ($existing) {
                $request->getSession()->set('pending_inventory', [
                    'article_id' => $article->getId(),
                    'quantity' => $inventory->getQuantity(),
                    'existing_id' => $existing->getId(),
                    'existing_quantity' => $existing->getQuantity(),
                    'family_id' => $familyId,
                ]);

                $redirectRoute = $this->generateUrl('inventories.confirm-duplicate', ['id' => $article->getId()]);
                if ($familyId) {
                    $redirectRoute .= '?family_id=' . $familyId;
                }

                $this->addFlash('warning', 'Cet article a déjà été inventorié aujourd\'hui !');

                return $this->redirect($redirectRoute);
            }

            $inventory->setArticle($article);
            $inventory->setDate(new \DateTime());
            $this->entityManager->persist($inventory);
            $this->entityManager->flush();

            $this->addFlash('success', 'Inventaire enregistré avec succès !');

            $redirectRoute = $this->generateUrl('inventories.search');
            if ($familyId) {
                $redirectRoute .= '?family_id=' . $familyId;
            }

            return $this->redirect($redirectRoute);
        }

        return $this->render('inventories/create.html.twig', [
            'article' => $article,
            'todayInventories' => $this->getTodayInventories(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}/confirm-duplicate', name: 'inventories.confirm-duplicate', methods: ['GET'])]
    public function confirmDuplicate(Request $request, Article $article): Response
    {
        $pending = $request->getSession()->get('pending_inventory');

        if (!$pending) {
            return $this->redirectToRoute('inventories.index');
        }

        $existing = $this->inventoryRepository->find($pending['existing_id']);

        if (!$existing) {
            return $this->redirectToRoute('inventories.index');
        }

        return $this->render('inventories/confirm-duplicate.html.twig', [
            'article' => $article,
            'existing' => $existing,
            'pending' => $pending,
        ]);
    }

    #[Route('/article/{id}/process-duplicate', name: 'inventories.process-duplicate', methods: ['POST'])]
    public function processDuplicate(Request $request, Article $article): RedirectResponse
    {
        $session = $request->getSession();
        $pending = $session->get('pending_inventory');

        if (!$pending) {
            return $this->redirectToRoute('inventories.index');
        }

        $session->remove('pending_inventory');

        $familyId = $request->request->get('family_id', $pending['family_id'] ?? null);

        $existing = $this->inventoryRepository->find($pending['existing_id']);
        $action = $request->request->get('action');

        switch ($action) {
            case 'add':
                $existing->setQuantity($existing->getQuantity() + $pending['quantity']);
                break;
            case 'replace':
                $existing->setQuantity($pending['quantity']);
                break;
            case 'cancel':
            default:
                break;
        }

        if ($action !== 'cancel') {
            $this->entityManager->flush();
        }

        $redirectRoute = $this->generateUrl('inventories.search');
        if ($familyId) {
            $redirectRoute .= '?family_id=' . $familyId;
        }

        $this->addFlash('success', 'Inventaire mis à jour !');

        return $this->redirect($redirectRoute);
    }

    #[Route('/{id}', name: 'inventories.destroy', methods: ['DELETE'])]
    public function destroy(Inventory $inventory): RedirectResponse
    {
        $article = $inventory->getArticle();
        $familyId = $article && $article->getFamily() ? $article->getFamily()->getId() : null;

        $this->entityManager->remove($inventory);
        $this->entityManager->flush();

        $redirectRoute = $this->generateUrl('inventories.search');
        if ($familyId) {
            $redirectRoute .= '?family_id=' . $familyId;
        }

        $this->addFlash('success', 'Inventaire supprimé avec succès !');

        return $this->redirect($redirectRoute);
    }

    private function getTodayRange(): array
    {
        $todayStart = (new \DateTime())->setTime(0, 0, 0);
        $tomorrowStart = (clone $todayStart)->modify('+1 day');
        return [$todayStart, $tomorrowStart];
    }

    private function getTodayInventories(): array
    {
        [$todayStart, $tomorrowStart] = $this->getTodayRange();

        return $this->inventoryRepository->createQueryBuilder('i')
            ->leftJoin('i.article', 'a')
            ->leftJoin('a.packaging', 'p')
            ->leftJoin('a.unit', 'u')
            ->leftJoin('a.priceHistories', 'ph')
            ->where('i.date >= :todayStart')
            ->andWhere('i.date < :tomorrowStart')
            ->setParameter('todayStart', $todayStart)
            ->setParameter('tomorrowStart', $tomorrowStart)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

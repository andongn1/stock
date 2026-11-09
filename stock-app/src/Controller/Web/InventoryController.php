<?php

namespace App\Controller\Web;

use App\Controller\Controller;
use App\Entity\Article;
use App\Entity\Family;
use App\Entity\Inventory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InventoryController extends Controller
{
    #[Route('/inventory', name: 'inventories.index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        return $this->search($request, $em);
    }

    #[Route('/inventory/search', name: 'inventories.search', methods: ['GET', 'POST'])]
    public function search(Request $request, EntityManagerInterface $em): Response
    {
        $search = $request->query->get('search', '');
        $familyId = $request->query->get('family_id', null);

        $queryBuilder = $em->getRepository(Article::class)->createQueryBuilder('a')
            ->leftJoin('a.packaging', 'p')
            ->leftJoin('a.unit', 'u')
            ->leftJoin('a.family', 'f')
            ->leftJoin('a.priceHistories', 'ph')
            ->orderBy('a.name', 'ASC');

        if ($familyId) {
            $queryBuilder->andWhere('a.family = :familyId')->setParameter('familyId', $familyId);
        }

        if (!empty($search)) {
            $queryBuilder->andWhere('a.name LIKE :search')->setParameter('search', '%' . $search . '%');
        }

        $articles = $queryBuilder->getQuery()->getResult();

        $todayInventories = $em->getRepository(Inventory::class)->findBy([
            'date' => new \DateTime('today'),
        ], ['createdAt' => 'DESC']);

        $families = $em->getRepository(Family::class)->findAll();

        $previousStocks = [];
        $todayStocks = [];

        foreach ($articles as $article) {
            $lastInventory = $em->getRepository(Inventory::class)->findOneBy([
                'article' => $article,
            ], ['date' => 'DESC']);
            $previousStocks[$article->getId()] = $lastInventory ? $lastInventory->getQuantity() : 0;

            $todayInventory = $em->getRepository(Inventory::class)->findOneBy([
                'article' => $article,
                'date' => new \DateTime('today'),
            ]);
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

    #[Route('/inventory/article/{id}', name: 'inventories.create', methods: ['GET'])]
    public function create(Article $article, EntityManagerInterface $em): Response
    {
        $todayInventories = $em->getRepository(Inventory::class)->findBy([
            'date' => new \DateTime('today'),
        ], ['createdAt' => 'DESC']);

        return $this->render('inventories/create.html.twig', [
            'article' => $article,
            'todayInventories' => $todayInventories,
        ]);
    }

    #[Route('/inventory/article/{id}', name: 'inventories.store', methods: ['POST'])]
    public function store(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $quantity = $request->request->get('quantity');
        $familyId = $request->query->get('family_id');

        if (!$quantity) {
            return $this->redirectToRoute('inventories.search', ['family_id' => $familyId]);
        }

        $existing = $em->getRepository(Inventory::class)->findOneBy([
            'article' => $article,
            'date' => new \DateTime('today'),
        ]);

        if ($existing) {
            $request->getSession()->set('pending_inventory', [
                'article_id' => $article->getId(),
                'quantity' => $quantity,
                'existing_id' => $existing->getId(),
                'existing_quantity' => $existing->getQuantity(),
                'family_id' => $familyId,
            ]);

            return $this->redirectToRoute('inventories.confirm_duplicate', [
                'id' => $article->getId(),
                'family_id' => $familyId,
            ]);
        }

        $inventory = new Inventory();
        $inventory->setArticle($article);
        $inventory->setQuantity($quantity);
        $inventory->setDate(new \DateTime());

        $em->persist($inventory);
        $em->flush();

        return $this->redirectToRoute('inventories.search', ['family_id' => $familyId]);
    }

    #[Route('/inventory/article/{id}/confirm-duplicate', name: 'inventories.confirm_duplicate')]
    public function confirmDuplicate(Article $article, Request $request): Response
    {
        $pending = $request->getSession()->get('pending_inventory');

        if (!$pending) {
            return $this->redirectToRoute('inventories.index');
        }

        $existing = $pending['existing_id'] ? $this->getDoctrine()->getRepository(Inventory::class)->find($pending['existing_id']) : null;

        return $this->render('inventories/confirm_duplicate.html.twig', [
            'article' => $article,
            'existing' => $existing,
            'pending' => $pending,
        ]);
    }

    #[Route('/inventory/article/{id}/process-duplicate', name: 'inventories.process_duplicate', methods: ['POST'])]
    public function processDuplicate(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $pending = $request->getSession()->get('pending_inventory');
        $request->getSession()->remove('pending_inventory');

        if (!$pending) {
            return $this->redirectToRoute('inventories.index');
        }

        $familyId = $request->query->get('family_id', $pending['family_id'] ?? null);
        $action = $request->request->get('action');

        $existing = $pending['existing_id'] ? $em->getRepository(Inventory::class)->find($pending['existing_id']) : null;

        if ($existing && $action === 'add') {
            $existing->setQuantity((string)($existing->getQuantity() + $pending['quantity']));
            $em->flush();
        } elseif ($existing && $action === 'replace') {
            $existing->setQuantity($pending['quantity']);
            $em->flush();
        }

        return $this->redirectToRoute('inventories.search', ['family_id' => $familyId]);
    }

    #[Route('/inventory/{id}', name: 'inventories.destroy', methods: ['DELETE'])]
    public function destroy(Inventory $inventory, EntityManagerInterface $em): Response
    {
        $familyId = $inventory->getArticle()?->getFamily()?->getId();
        $em->remove($inventory);
        $em->flush();

        return $this->redirectToRoute('inventories.search', ['family_id' => $familyId]);
    }
}

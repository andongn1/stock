<?php

namespace App\Controller\Web;

use App\Controller\Controller;
use App\Entity\Article;
use App\Entity\Entry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntryController extends Controller
{
    #[Route('/entries', name: 'entries.index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        return $this->search($request, $em);
    }

    #[Route('/entries/search', name: 'entries.search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $em): Response
    {
        $search = $request->query->get('search', '');

        $articles = [];
        if (!empty($search)) {
            $articles = $em->getRepository(Article::class)->createQueryBuilder('a')
                ->leftJoin('a.packaging', 'p')
                ->leftJoin('a.unit', 'u')
                ->leftJoin('a.priceHistories', 'ph')
                ->where('a.name LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->orderBy('a.name', 'ASC')
                ->setMaxResults(10)
                ->getQuery()->getResult();
        }

        $todayEntries = $em->getRepository(Entry::class)->findBy([
            'date' => new \DateTime('today'),
        ], ['createdAt' => 'DESC']);

        return $this->render('entries/index.html.twig', [
            'articles' => $articles,
            'search' => $search,
            'todayEntries' => $todayEntries,
        ]);
    }

    #[Route('/entries/article/{id}', name: 'entries.create', methods: ['GET'])]
    public function create(Article $article, EntityManagerInterface $em): Response
    {
        $todayEntries = $em->getRepository(Entry::class)->findBy([
            'date' => new \DateTime('today'),
        ], ['createdAt' => 'DESC']);

        return $this->render('entries/create.html.twig', [
            'article' => $article,
            'todayEntries' => $todayEntries,
        ]);
    }

    #[Route('/entries/article/{id}', name: 'entries.store', methods: ['POST'])]
    public function store(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $quantity = $request->request->get('quantity');

        if (!$quantity) {
            return $this->redirectToRoute('entries.index');
        }

        $entry = new Entry();
        $entry->setArticle($article);
        $entry->setQuantity($quantity);
        $entry->setDate(new \DateTime());

        $em->persist($entry);
        $em->flush();

        return $this->redirectToRoute('entries.index');
    }

    #[Route('/entries/{id}', name: 'entries.destroy', methods: ['DELETE'])]
    public function destroy(Entry $entry, EntityManagerInterface $em): Response
    {
        $em->remove($entry);
        $em->flush();

        return $this->redirectToRoute('entries.index');
    }
}

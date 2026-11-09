<?php

namespace App\Controller\Web;

use App\Entity\Article;
use App\Entity\Entry;
use App\Form\EntryType;
use App\Repository\EntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/entries')]
class EntryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EntryRepository $entryRepository,
        private \App\Repository\ArticleRepository $articleRepository
    ) {
    }

    #[Route('/', name: 'entries.index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->search($request);
    }

    #[Route('/search', name: 'entries.search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $search = $request->query->get('search', '');

        $articles = [];
        if (!empty($search)) {
            $queryBuilder = $this->articleRepository->createQueryBuilder('a');
            $queryBuilder
                ->leftJoin('a.packaging', 'p')
                ->leftJoin('a.unit', 'u')
                ->where('a.name LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->orderBy('a.name', 'ASC')
                ->setMaxResults(10);
            $articles = $queryBuilder->getQuery()->getResult();
        }

        $today = new \DateTime();
        $todayString = $today->format('Y-m-d');

        $todayEntries = $this->entryRepository->createQueryBuilder('e')
            ->leftJoin('e.article', 'a')
            ->leftJoin('a.packaging', 'p')
            ->leftJoin('a.unit', 'u')
            ->leftJoin('a.priceHistories', 'ph')
            ->where('DATE(e.date) = :today')
            ->setParameter('today', $todayString)
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('entries/index.html.twig', [
            'articles' => $articles,
            'search' => $search,
            'todayEntries' => $todayEntries,
        ]);
    }

    #[Route('/article/{id}', name: 'entries.create', methods: ['GET'])]
    public function create(Article $article): Response
    {
        $todayEntries = $this->entryRepository->createQueryBuilder('e')
            ->leftJoin('e.article', 'a')
            ->leftJoin('a.packaging', 'p')
            ->leftJoin('a.unit', 'u')
            ->where('DATE(e.date) = :today')
            ->setParameter('today', (new \DateTime())->format('Y-m-d'))
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $form = $this->createForm(EntryType::class, new Entry());

        return $this->render('entries/create.html.twig', [
            'article' => $article,
            'todayEntries' => $todayEntries,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}', name: 'entries.store', methods: ['POST'])]
    public function store(Request $request, Article $article): RedirectResponse
    {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entry->setArticle($article);
            $entry->setDate(new \DateTime());
            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            $this->addFlash('success', 'Entrée de stock enregistrée avec succès !');

            return $this->redirectToRoute('entries.index');
        }

        $todayEntries = $this->entryRepository->createQueryBuilder('e')
            ->leftJoin('e.article', 'a')
            ->leftJoin('a.packaging', 'p')
            ->leftJoin('a.unit', 'u')
            ->where('DATE(e.date) = :today')
            ->setParameter('today', (new \DateTime())->format('Y-m-d'))
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('entries/create.html.twig', [
            'article' => $article,
            'todayEntries' => $todayEntries,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'entries.destroy', methods: ['DELETE'])]
    public function destroy(Entry $entry): RedirectResponse
    {
        $this->entityManager->remove($entry);
        $this->entityManager->flush();

        $this->addFlash('success', 'Entrée de stock supprimée avec succès !');

        return $this->redirectToRoute('entries.index');
    }
}

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

        $todayEntries = $this->getTodayEntries();

        return $this->render('entries/index.html.twig', [
            'articles' => $articles,
            'search' => $search,
            'todayEntries' => $todayEntries,
        ]);
    }

    #[Route('/article/{id}', name: 'entries.create', methods: ['GET'])]
    public function create(Article $article): Response
    {
        $form = $this->createForm(EntryType::class, new Entry());

        return $this->render('entries/create.html.twig', [
            'article' => $article,
            'todayEntries' => $this->getTodayEntries(),
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

        return $this->render('entries/create.html.twig', [
            'article' => $article,
            'todayEntries' => $this->getTodayEntries(),
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

    private function getTodayEntries(): array
    {
        $todayStart = (new \DateTime())->setTime(0, 0, 0);
        $tomorrowStart = (clone $todayStart)->modify('+1 day');

        return $this->entryRepository->createQueryBuilder('e')
            ->leftJoin('e.article', 'a')
            ->leftJoin('a.packaging', 'p')
            ->leftJoin('a.unit', 'u')
            ->leftJoin('a.priceHistories', 'ph')
            ->where('e.date >= :todayStart')
            ->andWhere('e.date < :tomorrowStart')
            ->setParameter('todayStart', $todayStart)
            ->setParameter('tomorrowStart', $tomorrowStart)
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

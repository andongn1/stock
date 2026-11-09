<?php

namespace App\Controller\Web;

use App\Entity\Packaging;
use App\Form\PackagingType;
use App\Repository\PackagingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/packagings')]
class PackagingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PackagingRepository $packagingRepository
    ) {
    }

    #[Route('/', name: 'packagings.index', methods: ['GET'])]
    public function index(): Response
    {
        $packagings = $this->packagingRepository->findAll();
        
        return $this->render('packagings/index.html.twig', [
            'packagings' => $packagings,
        ]);
    }

    #[Route('/create', name: 'packagings.create', methods: ['GET'])]
    public function create(): Response
    {
        $packaging = new Packaging();
        $form = $this->createForm(PackagingType::class, $packaging);

        return $this->render('packagings/create.html.twig', [
            'packaging' => $packaging,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'packagings.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $packaging = new Packaging();
        $form = $this->createForm(PackagingType::class, $packaging);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($packaging);
            $this->entityManager->flush();

            $this->addFlash('success', 'Conditionnement cree avec succes !');
            return $this->redirectToRoute('packagings.index');
        }

        return $this->render('packagings/create.html.twig', [
            'packaging' => $packaging,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'packagings.edit', methods: ['GET'])]
    public function edit(Packaging $packaging): Response
    {
        $form = $this->createForm(PackagingType::class, $packaging);

        return $this->render('packagings/edit.html.twig', [
            'packaging' => $packaging,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'packagings.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Packaging $packaging): Response
    {
        $form = $this->createForm(PackagingType::class, $packaging);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Conditionnement mis a jour avec succes !');
            return $this->redirectToRoute('packagings.index');
        }

        return $this->render('packagings/edit.html.twig', [
            'packaging' => $packaging,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'packagings.destroy', methods: ['DELETE'])]
    public function destroy(Packaging $packaging): Response
    {
        $this->entityManager->remove($packaging);
        $this->entityManager->flush();

        $this->addFlash('success', 'Conditionnement supprime avec succes !');
        return $this->redirectToRoute('packagings.index');
    }
}

<?php

namespace App\Controller\Web;

use App\Entity\Vat;
use App\Form\VatType;
use App\Repository\VatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/vats')]
class VatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private VatRepository $vatRepository
    ) {
    }

    #[Route('/', name: 'vats.index', methods: ['GET'])]
    public function index(): Response
    {
        $vats = $this->vatRepository->findAll();
        
        return $this->render('vats/index.html.twig', [
            'vats' => $vats,
        ]);
    }

    #[Route('/create', name: 'vats.create', methods: ['GET'])]
    public function create(): Response
    {
        $vat = new Vat();
        $form = $this->createForm(VatType::class, $vat);

        return $this->render('vats/create.html.twig', [
            'vat' => $vat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'vats.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $vat = new Vat();
        $form = $this->createForm(VatType::class, $vat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($vat);
            $this->entityManager->flush();

            $this->addFlash('success', 'TVA creee avec succes !');

            return $this->redirectToRoute('vats.index');
        }

        return $this->render('vats/create.html.twig', [
            'vat' => $vat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'vats.edit', methods: ['GET'])]
    public function edit(Vat $vat): Response
    {
        $form = $this->createForm(VatType::class, $vat);

        return $this->render('vats/edit.html.twig', [
            'vat' => $vat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'vats.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Vat $vat): Response
    {
        $form = $this->createForm(VatType::class, $vat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'TVA mise a jour avec succes !');

            return $this->redirectToRoute('vats.index');
        }

        return $this->render('vats/edit.html.twig', [
            'vat' => $vat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'vats.destroy', methods: ['DELETE'])]
    public function destroy(Vat $vat): Response
    {
        $this->entityManager->remove($vat);
        $this->entityManager->flush();

        $this->addFlash('success', 'TVA supprimee avec succes !');

        return $this->redirectToRoute('vats.index');
    }
}

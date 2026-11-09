<?php

namespace App\Controller\Web;

use App\Entity\Supplier;
use App\Form\SupplierType;
use App\Repository\SupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/suppliers')]
class SupplierController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SupplierRepository $supplierRepository
    ) {
    }

    #[Route('/', name: 'suppliers.index', methods: ['GET'])]
    public function index(): Response
    {
        $suppliers = $this->supplierRepository->findAll();
        
        return $this->render('suppliers/index.html.twig', [
            'suppliers' => $suppliers,
        ]);
    }

    #[Route('/create', name: 'suppliers.create', methods: ['GET'])]
    public function create(): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierType::class, $supplier);

        return $this->render('suppliers/create.html.twig', [
            'supplier' => $supplier,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'suppliers.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($supplier);
            $this->entityManager->flush();

            $this->addFlash('success', 'Fournisseur cree avec succes !');

            return $this->redirectToRoute('suppliers.index');
        }

        return $this->render('suppliers/create.html.twig', [
            'supplier' => $supplier,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'suppliers.edit', methods: ['GET'])]
    public function edit(Supplier $supplier): Response
    {
        $form = $this->createForm(SupplierType::class, $supplier);

        return $this->render('suppliers/edit.html.twig', [
            'supplier' => $supplier,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'suppliers.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Supplier $supplier): Response
    {
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Fournisseur mis a jour avec succes !');

            return $this->redirectToRoute('suppliers.index');
        }

        return $this->render('suppliers/edit.html.twig', [
            'supplier' => $supplier,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'suppliers.destroy', methods: ['DELETE'])]
    public function destroy(Supplier $supplier): Response
    {
        $this->entityManager->remove($supplier);
        $this->entityManager->flush();

        $this->addFlash('success', 'Fournisseur supprime avec succes !');

        return $this->redirectToRoute('suppliers.index');
    }
}

<?php

namespace App\Controller\Web;

use App\Controller\Controller;
use App\Entity\Supplier;
use App\Form\SupplierType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupplierController extends Controller
{
    #[Route('/suppliers', name: 'suppliers.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $suppliers = $em->getRepository(Supplier::class)->findAll();

        return $this->render('suppliers/index.html.twig', [
            'suppliers' => $suppliers,
        ]);
    }

    #[Route('/suppliers/create', name: 'suppliers.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($supplier);
            $em->flush();

            return $this->redirectToRoute('suppliers.index');
        }

        return $this->render('suppliers/create.html.twig', [
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }

    #[Route('/suppliers/{id}/edit', name: 'suppliers.edit')]
    public function edit(Supplier $supplier, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('suppliers.index');
        }

        return $this->render('suppliers/edit.html.twig', [
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }

    #[Route('/suppliers/{id}', name: 'suppliers.destroy', methods: ['DELETE'])]
    public function destroy(Supplier $supplier, EntityManagerInterface $em): Response
    {
        $em->remove($supplier);
        $em->flush();

        return $this->redirectToRoute('suppliers.index');
    }
}

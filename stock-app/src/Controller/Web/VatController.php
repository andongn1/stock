<?php

namespace App\Controller\Web;

use App\Controller\Controller;
use App\Entity\Vat;
use App\Form\VatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VatController extends Controller
{
    #[Route('/vats', name: 'vats.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $vats = $em->getRepository(Vat::class)->findAll();

        return $this->render('vats/index.html.twig', [
            'vats' => $vats,
        ]);
    }

    #[Route('/vats/create', name: 'vats.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $vat = new Vat();
        $form = $this->createForm(VatType::class, $vat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($vat);
            $em->flush();

            return $this->redirectToRoute('vats.index');
        }

        return $this->render('vats/create.html.twig', [
            'vat' => $vat,
            'form' => $form,
        ]);
    }

    #[Route('/vats/{id}/edit', name: 'vats.edit')]
    public function edit(Vat $vat, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VatType::class, $vat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('vats.index');
        }

        return $this->render('vats/edit.html.twig', [
            'vat' => $vat,
            'form' => $form,
        ]);
    }

    #[Route('/vats/{id}', name: 'vats.destroy', methods: ['DELETE'])]
    public function destroy(Vat $vat, EntityManagerInterface $em): Response
    {
        $em->remove($vat);
        $em->flush();

        return $this->redirectToRoute('vats.index');
    }
}

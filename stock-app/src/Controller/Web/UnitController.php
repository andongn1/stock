<?php

namespace App\Controller\Web;

use App\Controller\Controller;
use App\Entity\Unit;
use App\Form\UnitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UnitController extends Controller
{
    #[Route('/units', name: 'units.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $units = $em->getRepository(Unit::class)->findAll();

        return $this->render('units/index.html.twig', [
            'units' => $units,
        ]);
    }

    #[Route('/units/create', name: 'units.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $unit = new Unit();
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($unit);
            $em->flush();

            return $this->redirectToRoute('units.index');
        }

        return $this->render('units/create.html.twig', [
            'unit' => $unit,
            'form' => $form,
        ]);
    }

    #[Route('/units/{id}/edit', name: 'units.edit')]
    public function edit(Unit $unit, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('units.index');
        }

        return $this->render('units/edit.html.twig', [
            'unit' => $unit,
            'form' => $form,
        ]);
    }

    #[Route('/units/{id}', name: 'units.destroy', methods: ['DELETE'])]
    public function destroy(Unit $unit, EntityManagerInterface $em): Response
    {
        $em->remove($unit);
        $em->flush();

        return $this->redirectToRoute('units.index');
    }
}

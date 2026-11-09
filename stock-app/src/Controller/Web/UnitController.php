<?php

namespace App\Controller\Web;

use App\Entity\Unit;
use App\Form\UnitType;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/units')]
class UnitController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UnitRepository $unitRepository
    ) {
    }

    #[Route('/', name: 'units.index', methods: ['GET'])]
    public function index(): Response
    {
        $units = $this->unitRepository->findAll();
        
        return $this->render('units/index.html.twig', [
            'units' => $units,
        ]);
    }

    #[Route('/create', name: 'units.create', methods: ['GET'])]
    public function create(): Response
    {
        $unit = new Unit();
        $form = $this->createForm(UnitType::class, $unit);

        return $this->render('units/create.html.twig', [
            'unit' => $unit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'units.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $unit = new Unit();
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($unit);
            $this->entityManager->flush();

            $this->addFlash('success', 'Unite creee avec succes !');
            return $this->redirectToRoute('units.index');
        }

        return $this->render('units/create.html.twig', [
            'unit' => $unit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'units.edit', methods: ['GET'])]
    public function edit(Unit $unit): Response
    {
        $form = $this->createForm(UnitType::class, $unit);

        return $this->render('units/edit.html.twig', [
            'unit' => $unit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'units.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Unit $unit): Response
    {
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Unite mise a jour avec succes !');
            return $this->redirectToRoute('units.index');
        }

        return $this->render('units/edit.html.twig', [
            'unit' => $unit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'units.destroy', methods: ['DELETE'])]
    public function destroy(Unit $unit): Response
    {
        $this->entityManager->remove($unit);
        $this->entityManager->flush();

        $this->addFlash('success', 'Unite supprimee avec succes !');
        return $this->redirectToRoute('units.index');
    }
}

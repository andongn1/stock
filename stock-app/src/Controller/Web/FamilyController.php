<?php

namespace App\Controller\Web;

use App\Entity\Family;
use App\Form\FamilyType;
use App\Repository\FamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/families')]
class FamilyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FamilyRepository $familyRepository
    ) {
    }

    #[Route('/', name: 'families.index', methods: ['GET'])]
    public function index(): Response
    {
        $families = $this->familyRepository->findAll();
        
        return $this->render('families/index.html.twig', [
            'families' => $families,
        ]);
    }

    #[Route('/create', name: 'families.create', methods: ['GET'])]
    public function create(): Response
    {
        $family = new Family();
        $form = $this->createForm(FamilyType::class, $family);

        return $this->render('families/create.html.twig', [
            'family' => $family,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'families.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $family = new Family();
        $form = $this->createForm(FamilyType::class, $family);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($family);
            $this->entityManager->flush();

            $this->addFlash('success', 'Famille creee avec succes !');

            return $this->redirectToRoute('families.index');
        }

        return $this->render('families/create.html.twig', [
            'family' => $family,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'families.edit', methods: ['GET'])]
    public function edit(Family $family): Response
    {
        $form = $this->createForm(FamilyType::class, $family);

        return $this->render('families/edit.html.twig', [
            'family' => $family,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'families.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Family $family): Response
    {
        $form = $this->createForm(FamilyType::class, $family);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Famille mise a jour avec succes !');

            return $this->redirectToRoute('families.index');
        }

        return $this->render('families/edit.html.twig', [
            'family' => $family,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'families.destroy', methods: ['DELETE'])]
    public function destroy(Family $family): Response
    {
        $this->entityManager->remove($family);
        $this->entityManager->flush();

        $this->addFlash('success', 'Famille supprimee avec succes !');

        return $this->redirectToRoute('families.index');
    }
}

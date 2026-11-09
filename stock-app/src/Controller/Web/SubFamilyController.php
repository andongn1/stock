<?php

namespace App\Controller\Web;

use App\Entity\SubFamily;
use App\Form\SubFamilyType;
use App\Repository\SubFamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sub-familles')]
class SubFamilyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SubFamilyRepository $subFamilyRepository
    ) {
    }

    #[Route('/', name: 'sub-familles.index', methods: ['GET'])]
    public function index(): Response
    {
        $subFamilles = $this->subFamilyRepository->findAll();
        
        return $this->render('sub_families/index.html.twig', [
            'subFamilles' => $subFamilles,
        ]);
    }

    #[Route('/create', name: 'sub-familles.create', methods: ['GET'])]
    public function create(): Response
    {
        $subFamily = new SubFamily();
        $form = $this->createForm(SubFamilyType::class, $subFamily);

        return $this->render('sub_families/create.html.twig', [
            'subFamily' => $subFamily,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'sub-familles.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $subFamily = new SubFamily();
        $form = $this->createForm(SubFamilyType::class, $subFamily);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($subFamily);
            $this->entityManager->flush();

            $this->addFlash('success', 'Sous-famille creee avec succes !');

            return $this->redirectToRoute('sub-familles.index');
        }

        return $this->render('sub_families/create.html.twig', [
            'subFamily' => $subFamily,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'sub-familles.edit', methods: ['GET'])]
    public function edit(SubFamily $subFamily): Response
    {
        $form = $this->createForm(SubFamilyType::class, $subFamily);

        return $this->render('sub_families/edit.html.twig', [
            'subFamily' => $subFamily,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'sub-familles.update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, SubFamily $subFamily): Response
    {
        $form = $this->createForm(SubFamilyType::class, $subFamily);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Sous-famille mise a jour avec succes !');

            return $this->redirectToRoute('sub-familles.index');
        }

        return $this->render('sub_families/edit.html.twig', [
            'subFamily' => $subFamily,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'sub-familles.destroy', methods: ['DELETE'])]
    public function destroy(SubFamily $subFamily): Response
    {
        $this->entityManager->remove($subFamily);
        $this->entityManager->flush();

        $this->addFlash('success', 'Sous-famille supprimee avec succes !');

        return $this->redirectToRoute('sub-familles.index');
    }
}

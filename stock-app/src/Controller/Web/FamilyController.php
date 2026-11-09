<?php

namespace App\Controller\Web;

use App\Controller\Controller;
use App\Entity\Family;
use App\Form\FamilyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FamilyController extends Controller
{
    #[Route('/families', name: 'families.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $families = $em->getRepository(Family::class)->findAll();

        return $this->render('families/index.html.twig', [
            'families' => $families,
        ]);
    }

    #[Route('/families/create', name: 'families.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $family = new Family();
        $form = $this->createForm(FamilyType::class, $family);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($family);
            $em->flush();

            return $this->redirectToRoute('families.index');
        }

        return $this->render('families/create.html.twig', [
            'family' => $family,
            'form' => $form,
        ]);
    }

    #[Route('/families/{id}/edit', name: 'families.edit')]
    public function edit(Family $family, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FamilyType::class, $family);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('families.index');
        }

        return $this->render('families/edit.html.twig', [
            'family' => $family,
            'form' => $form,
        ]);
    }

    #[Route('/families/{id}', name: 'families.destroy', methods: ['DELETE'])]
    public function destroy(Family $family, EntityManagerInterface $em): Response
    {
        $em->remove($family);
        $em->flush();

        return $this->redirectToRoute('families.index');
    }
}

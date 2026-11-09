<?php

namespace App\Controller\Web;

use App\Controller\Controller;
use App\Entity\Family;
use App\Entity\SubFamily;
use App\Form\SubFamilyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubFamilyController extends Controller
{
    #[Route('/sub-familles', name: 'sub_familles.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $subFamilles = $em->getRepository(SubFamily::class)->findAll();
        $families = $em->getRepository(Family::class)->findAll();

        return $this->render('sub_familles/index.html.twig', [
            'subFamilles' => $subFamilles,
            'families' => $families,
        ]);
    }

    #[Route('/sub-familles/create', name: 'sub_familles.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $subFamily = new SubFamily();
        $form = $this->createForm(SubFamilyType::class, $subFamily);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($subFamily);
            $em->flush();

            return $this->redirectToRoute('sub_familles.index');
        }

        return $this->render('sub_familles/create.html.twig', [
            'subFamily' => $subFamily,
            'form' => $form,
        ]);
    }

    #[Route('/sub-familles/{id}/edit', name: 'sub_familles.edit')]
    public function edit(SubFamily $subFamily, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SubFamilyType::class, $subFamily);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('sub_familles.index');
        }

        return $this->render('sub_familles/edit.html.twig', [
            'subFamily' => $subFamily,
            'form' => $form,
        ]);
    }

    #[Route('/sub-familles/{id}', name: 'sub_familles.destroy', methods: ['DELETE'])]
    public function destroy(SubFamily $subFamily, EntityManagerInterface $em): Response
    {
        $em->remove($subFamily);
        $em->flush();

        return $this->redirectToRoute('sub_familles.index');
    }
}

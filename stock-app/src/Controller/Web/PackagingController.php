<?php

namespace App\Controller\Web;

use App\Controller\Controller;
use App\Entity\Packaging;
use App\Form\PackagingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PackagingController extends Controller
{
    #[Route('/packagings', name: 'packagings.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $packagings = $em->getRepository(Packaging::class)->findAll();

        return $this->render('packagings/index.html.twig', [
            'packagings' => $packagings,
        ]);
    }

    #[Route('/packagings/create', name: 'packagings.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $packaging = new Packaging();
        $form = $this->createForm(PackagingType::class, $packaging);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($packaging);
            $em->flush();

            return $this->redirectToRoute('packagings.index');
        }

        return $this->render('packagings/create.html.twig', [
            'packaging' => $packaging,
            'form' => $form,
        ]);
    }

    #[Route('/packagings/{id}/edit', name: 'packagings.edit')]
    public function edit(Packaging $packaging, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PackagingType::class, $packaging);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('packagings.index');
        }

        return $this->render('packagings/edit.html.twig', [
            'packaging' => $packaging,
            'form' => $form,
        ]);
    }

    #[Route('/packagings/{id}', name: 'packagings.destroy', methods: ['DELETE'])]
    public function destroy(Packaging $packaging, EntityManagerInterface $em): Response
    {
        $em->remove($packaging);
        $em->flush();

        return $this->redirectToRoute('packagings.index');
    }
}

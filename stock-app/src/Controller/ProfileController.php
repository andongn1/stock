<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use App\Form\PasswordFormType;
use App\Form\DeleteFormType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends Controller
{
    #[Route('/profile', name: 'profile.edit', methods: ['GET'])]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile', name: 'profile.update', methods: ['PATCH', 'POST'])]
    public function update(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->isDirty('email')) {
                $user->setEmailVerifiedAt(null);
            }
            $em->flush();

            return $this->redirectToRoute('profile.edit');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/profile', name: 'profile.destroy', methods: ['DELETE'])]
    public function destroy(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(DeleteFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data['password'] && password_verify($data['password'], $user->getPassword())) {
                $em->remove($user);
                $em->flush();
                $security->logout(false);
                return $this->redirectToRoute('app_root');
            }
        }

        return $this->redirectToRoute('profile.edit');
    }
}

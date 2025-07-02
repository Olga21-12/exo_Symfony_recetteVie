<?php

namespace App\Controller;

use App\Form\UserFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function show(TranslatorInterface $t): Response
    {
        if(!$this->getUser()){ 
            $this->addFlash("error", $t->trans("accountController.edit.mustLogin"));
            return $this->redirectToRoute("app_login");
        }

        return $this->render('account/show.html.twig');
    }

    #[Route('/account/edit', name: "app_account_edit")]
    public function edit(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response{

    if(!$this->getUser()){ 
            $this->addFlash("error", $translator->trans("accountController.edit.mustLogin"));
            return $this->redirectToRoute("app_login");
        }

/** @var User $user */

        $user=$this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user->setUpdatedAt(new DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', $translator->trans('Account successfuly updated !'));
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/edit.html.twig', [
                        'user' => $user,
                        'userForm' => $form->createView()
        ]);
    }
}

<?php

namespace App\Controller\Admin;

use App\Form\EditorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Editor;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

#[Route('/admin/editor')]
final class EditorController extends AbstractController
{
    protected const APP_EDITOR ='app_admin_editor';

    #[Route('', name: self::APP_EDITOR.'_index')]
    public function index(Request $request, EditorRepository $repo): Response
    {
        $editors = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repo->createQueryBuilder('b')),
            $request->query->get('page', 1),
            10
        );

        return $this->render('admin/editor/index.html.twig', [
            'editors' => $editors,
        ]);
    }

    #[Route('/new', name: self::APP_EDITOR.'_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: self::APP_EDITOR.'_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Editor $editor, Request $request, EntityManagerInterface $em): Response
    {
        $editor ??= new Editor();
        $form = $this->createForm(EditorType::class, $editor);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($editor);
            $em->flush();

            //TODO : Créer une page de confirmation pour rediriger vers celle-ci après création d'un éditeur - avec bouton pour ajouter un nouveau / voir l'éditeur ajouté
            return $this->redirectToRoute('app_admin_editor_new');
        }
        return $this->render('admin/editor/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: self::APP_EDITOR.'_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Editor $editor): Response
    {
        return $this->render('admin/editor/show.html.twig', [
            'editors' => $editor,
        ]);
    }
}

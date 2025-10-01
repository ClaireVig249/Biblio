<?php

namespace App\Controller\Admin;

use App\Form\AuthorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AuthorRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/author')]
final class AuthorController extends AbstractController
{
    protected const APP_AUTHOR = 'app_admin_author';

    #[isGranted('IS_AUTHENTICATED')]
    #[Route('', name: self::APP_AUTHOR .'_index')]
    public function index(AuthorRepository $repo, Request $request): Response
    {
        $authors = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repo->createQueryBuilder('b')),
            $request->query->get('page', 1),
            10
        );

        return $this->render('admin/author/index.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/new', name: self::APP_AUTHOR.'_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: self::APP_AUTHOR . '_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Author $author, Request $request, EntityManagerInterface $em): Response
    {
        if (null === $author) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $author ??= new Author();
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($author); // indique à l'em qu'il va devoir sauvegarder cette donnée en base
            $em->flush();

            //TODO : Créer une page de confirmation pour rediriger vers celle-ci après création d'un auteur - avec bouton pour ajouter un nouveau / voir l'auteur ajouté
            return $this->redirectToRoute('app_admin_author_new');
        }

        return $this->render('admin/author/new.html.twig', [
            'form' => $form,
        ]);
    }

    // TODO : changer logique : traitement d'un objet null doit se faire dans le controller avec levée d'exception et pas dans la vue
    #[Route('/{id}', name: self::APP_AUTHOR.'_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Author $author): Response
    {
        return $this->render('admin/author/show.html.twig', [
            'authors' => $author,
        ]);
    }
}

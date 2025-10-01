<?php

namespace App\Controller\Admin;

use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

#[Route('/admin/book')]
final class BookController extends AbstractController
{
    protected const APP_BOOK = 'app_admin_book';

    #[Route('', name: self::APP_BOOK.'_index')]
    public function index(Request $request, BookRepository $repo): Response
    {
        $books = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repo->createQueryBuilder('b')),
            $request->query->get('page', 1),
            10
        );

        return $this->render('admin/book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/new', name: self::APP_BOOK.'_new')]
    #[Route('/{id}/edit', name: self::APP_BOOK.'_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Book $book, Request $request, EntityManagerInterface $em): Response
    {
        /*if ($book) {
            $this->denyAccessUnlessGranted('book.is_creator', $book);
        }*/

        $book ??= new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getUser();
            if (!$book->getId() && $user instanceof User){
                $book->setCreatedBy($user);
            };
            $em->persist($book);
            $em->flush();

            // TODO : Créer une page de confirmation pour rediriger vers celle-ci après création d'un livre - avec bouton pour ajouter un nouveau / voir le livre ajouté
            return $this->redirectToRoute('app_admin_book_new');
        }

        return $this->render('admin/book/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: self::APP_BOOK.'_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Book $book): Response
    {
        return $this->render('admin/book/show.html.twig', [
            'books' => $book,
        ]);
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use AppBundle\Service\AuthorService;
use AppBundle\Service\BookService;
use AppBundle\Service\FileUploader;
use AppBundle\Service\AvatarFileUploader;
use AppBundle\Service\ReviewService;
use AppBundle\Service\SignatureFileUploader;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends Controller
{
    /**
     * Shows a list of authors
     *
     * @return Response
     */
    public function indexAction() : Response
    {
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();

        return $this->render(':author:index.html.twig', [
            'authors' => $authors
        ]);
    }

    /**
     * Shows all books for the chosen author
     *
     * @param Author $author
     * @return Response
     */
    public function showAction(Author $author, BookService $bookService, ReviewService $reviewService) : Response
    {
        $books = $author->getBooks();
        $books = $bookService->getPreparedBooks($books, $this->getUser(), $reviewService);

        return $this->render(':author:single.html.twig', [
            'author' => $author,
            'books' => $books,
        ]);
    }

    /**
     * Creates a new author
     *
     * @return Response
     */
    public function newAction(Request $request, AuthorService $authorService) : Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $authorService->add($author);
        }

        return $this->render(':author:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Allows editing for the chosen author
     *
     * @return Response
     */
    public function editAction(Author $author) : Response
    {

    }

    /**
     * Removes the chosen author
     *
     * @param Author $author
     * @return Response
     */
    public function removeAction(Author $author) : Response
    {

    }
}

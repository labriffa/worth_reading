<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use AppBundle\Service\AuthorService;
use AppBundle\Service\BookService;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 *
 *
 * Class AuthorController
 * @package AppBundle\Controller
 */
class AuthorController extends Controller
{
    /**
     * Shows a list of authors
     *
     * @Template(":author:index.html.twig")
     * @return Response
     */
    public function indexAction() : array
    {
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();

        return ['authors' => $authors];
    }

    /**
     * Shows all books for the chosen author
     *
     * @Template(":author:single.html.twig")
     * @param Author $author
     * @return Response
     */
    public function showAction(Author $author, BookService $bookService) : array
    {
        $books = $bookService->prepareBooks($author->getBooks());

        return ['author' => $author, 'books' => $books,];
    }

    /**
     * Creates a new author
     *
     * @Template(":author:new.html.twig")
     * @return Response
     */
    public function newAction(Request $request, AuthorService $authorService) : array
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $authorService->add($author);
        }

        return ['form' => $form->createView()];
    }
}

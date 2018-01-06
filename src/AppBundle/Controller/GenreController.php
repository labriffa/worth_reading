<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genre;

use AppBundle\Form\GenreType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GenreController extends Controller
{
    /**
     * Shows a list of genres
     *
     * @return Response
     */
    public function indexAction() : Response
    {
        $genres = $this->getDoctrine()->getRepository(Genre::class)->findAll();

        return $this->render(':genre:index.html.twig', [
           'genres' => $genres,
        ]);
    }

    /**
     * Shows a list of books for the chosen genre
     *
     * @param Genre $genre
     * @return Response
     */
    public function showAction(Genre $genre) : Response
    {
        $books = $genre->getBooks();

        return $this->render(':genre:single.html.twig', [
            'books' => $books,
            'genre' => $genre,
        ]);
    }

    /**
     * Creates a new genre
     *
     * @return Response
     */
    public function newAction(Request $request) : Response
    {
        $form = $this->createForm(GenreType::class, new Genre());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
        }

        return $this->render(':genre:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits the chosen genre
     *
     * @param Genre $genre
     * @return Response
     */
    public function editAction(Genre $genre) : Response
    {

    }

    /**
     * Removes the chosen genre
     *
     * @param Genre $genre
     * @return Response
     */
    public function removeGenre(Genre $genre) : Response
    {

    }
}

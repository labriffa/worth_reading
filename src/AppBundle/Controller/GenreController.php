<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genre;

use AppBundle\Form\GenreType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class GenreController extends Controller
{
    /**
     * Shows a list of genres
     *
     * @Template(":genre:index.html.twig")
     * @return Response
     */
    public function indexAction() : array
    {
        $genres = $this->getDoctrine()->getRepository(Genre::class)->findAll();

        return ['genres' => $genres];
    }

    /**
     * Shows a list of books for the chosen genre
     *
     * @Template(":genre:single.html.twig")
     * @param Genre $genre
     * @return Response
     */
    public function showAction(Genre $genre) : array
    {
        $books = $genre->getBooks();

        return ['books' => $books, 'genre' => $genre];
    }

    /**
     * Creates a new genre
     *
     * @Template(":genre:new.html.twig")
     * @return Response
     */
    public function newAction(Request $request) : array
    {
        $form = $this->createForm(GenreType::class, new Genre());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
        }

        return ['form' => $form->createView()];
    }
}

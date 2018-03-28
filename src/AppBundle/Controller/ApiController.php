<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Genre;
use AppBundle\Form\GenreType;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\Book;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends FOSRestController {

    // -=-=-=-=-=-=-=-= GENRES -=-=-=-=-=-=-=-= //

    /**
     * Retrieve all genres
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getGenresAction()
    {
        $genres = $this->getDoctrine()->getRepository(Genre::class)->findAll();
        return $this->handleView($this->view($genres));
    }

    /**
     * Retrieve a particular genre
     *
     * @param Genre $genre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getGenreAction(Genre $genre)
    {
        if(!$genre) {
            return $this->handleView($this->view("Genre ID does not exist", 400));
        }

        return $this->handleView($this->view($genre, 200));
    }

    /**
     * Create a new genre
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postGenreAction(Request $request)
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);

        // check if the content type matches the expected json
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }

        // submit the form
        $form->submit(json_decode($request->getContent(), true));

        // check if the form is valid
        if($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($genre);
            $em->flush();

            // set the header to point to the new resource
            return $this->handleView($this->view(null, 201)
                ->setLocation($this->generateUrl('worth_reading_api_get_genre', [
                    'id' => $genre->getId()
                ])));

        } else {
            return $this->handleView($this->view($form , 400));
        }
    }

    /**
     * Update a given genre
     *
     * @param Genre $genre
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putGenreAction(Genre $genre, Request $request)
    {
        // Check if this genre exists
        if(!$genre) {
            return $this->handleView($this->view(null, 404));
        } else {
            // create the form
            $form = $this->createForm(GenreType::class, $genre);

            // check if the content type matches the expected json
            if($request->getContentType() != 'json') {
                return $this->handleView($this->view(null, 404));
            }

            // submit the form
            $form->submit(json_decode($request->getContent(), true));

            // check if the form is valid
            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($genre);
                $em->flush();

                // set the header to point to the updated resource
                return $this->handleView($this->view(null, 200)
                    ->setLocation($this->generateUrl('worth_reading_api_get_genre', [
                        'id' => $genre->getId()
                    ])));

            } else {
                return $this->handleView($this->view($form , 400));
            }
        }
    }

    /**
     * Delete the given genre
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteGenreAction(Genre $genre)
    {
        if (!$genre) {
            return $this->handleView($this->view(null, 404));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($genre);
        $em->flush();

        return $this->handleView($this->view(null, 200));
    }
}


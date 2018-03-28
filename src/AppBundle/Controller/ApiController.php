<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Genre;
use AppBundle\Form\GenreType;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends BaseApiController {

    // -=-=-=-=-=-=-=-= GENRES -=-=-=-=-=-=-=-= //
    /**
     * Retrieve all genres
     *
     * @param Request $request
     * @return Response
     */
    public function getGenresAction(Request $request)
    {
        $genres = $this->getDoctrine()->getRepository(Genre::class)->findAll();

        return $this->paginatedResponse(
            $request,
            $genres,
            'worth_reading_api_get_genres'
        );
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
            return $this->createApiResponse(
                "Requested genre does not exit",
                Response::HTTP_BAD_REQUEST);
        }

        return $this->createApiResponse($genre);
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
            return $this->createApiResponse(
                "Invalid JSON",
                Response::HTTP_BAD_REQUEST
            );
        }

        // submit the form
        $form->submit(json_decode($request->getContent(), true));

        // check if the form is valid
        if($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($genre);
            $em->flush();

            // set the header to point to the new resource
            return $this->handleView($this->view(null, Response::HTTP_CREATED)
                ->setLocation($this->generateUrl('worth_reading_api_get_genre', [
                    'id' => $genre->getId()
                ])));

        } else {
            return $this->handleView($this->view($form , Response::HTTP_BAD_REQUEST));
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
            return $this->createApiResponse(null, Response::HTTP_BAD_REQUEST);
        } else {
            // create the form
            $form = $this->createForm(GenreType::class, $genre);

            // check if the content type matches the expected json
            if($request->getContentType() != 'json') {
                return $this->createApiResponse(null, Response::HTTP_BAD_REQUEST);
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
                return $this->createApiResponse($form, Response::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Delete a given genre
     *
     * @param Genre $genre
     * @return Response
     */
    public function deleteGenreAction(Genre $genre)
    {
        if (!$genre) {
            return $this->createApiResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($genre);
        $em->flush();

        return $this->createApiResponse(null, Response::HTTP_NO_CONTENT);
    }
}


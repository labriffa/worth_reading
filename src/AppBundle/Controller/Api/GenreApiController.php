<?php
namespace AppBundle\Controller\Api;

use AppBundle\Entity\Genre;
use AppBundle\Form\GenreType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;


class GenreApiController extends BaseApiController {

    /**
     * Retrieves Genres
     *
     * Returns a paginated collection of genre resources
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of genre resources",
     *     examples={
     *          "": {
     *                  { "name": "Horror", "_self" : "{ href: /api/v1/genres/1 }" }
     *              },
     *          "Pagination Links": {
     *              "self": {
     *                  "href": "/api/v1/genres?page=1&limit=10"
     *              },
     *                  "first": {
     *                      "href": "/api/v1/genres?page=1&limit=10"
     *              },
     *                  "last": {
     *                      "href": "/api/v1/genres?page=2&limit=10"
     *              },
     *                  "next": {
     *                      "href": "/api/v1/genres?page=2&limit=10"
     *              }
     *          }
     *     }
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     default=10,
     *     required=false,
     *     description="The number of genres to return"
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     default=1,
     *     description="The page index of the paginated response"
     * )
     *
     * @SWG\Tag(name="genres")
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
     * Retrieve a genre
     *
     * Retrieve a genre resource based on a given id
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the details of a genre resource associated with the requested ID",
     *     examples={
     *          "": {
     *                  { "id": 1, "name": "Horror", "_links": "_self : { href: /api/v1/genres/1 }" }
     *              }
     *     },
     * ),
     *
     * @SWG\Response(
     *     response=404,
     *     description="ID not found or invalid",
     *     examples={
     *          "": "A genre with that ID could not be found"
     *     }
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested genre"
     * )
     *
     * @SWG\Tag(name="genres")
     *
     * @param Integer integer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getGenreAction($id)
    {
        $genre = $this->getDoctrine()->getRepository(Genre::class)->find($id);

        if(!$genre) {
            return $this->createApiResponse(
                "Requested genre does not exit",
                Response::HTTP_BAD_REQUEST);
        }

        return $this->createApiResponse($genre);
    }

    /**
     * Create a genre
     *
     * Creates a genre resource
     *
     * @SWG\Response(
     *     response=200,
     *     description="Sets the location header to the newly created resource",
     * ),
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized Access",
     *     examples={
     *          "": "OAuth2 Authentication Required"
     *     }
     * ),
     *
     * @SWG\Parameter(
     *         description="The name of the genre",
     *         in="body",
     *         name="name",
     *         required=true,
     *         @SWG\Schema(type="Horror")
     *  )
     *
     * @SWG\Tag(name="genres")
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
     * Update a genre
     *
     * Updates a genre resource
     *
     * @SWG\Response(
     *     response=200,
     *     description="Sets the location header to the newly created resource",
     * ),
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized Access",
     *     examples={
     *          "": "OAuth2 Authentication Required"
     *     }
     * ),
     *
     * @SWG\Response(
     *     response=404,
     *     description="A genre with that id could not be found"
     * ),
     *
     * @SWG\Parameter(
     *         description="The name of the genre",
     *         in="body",
     *         name="name",
     *         required=true,
     *         @SWG\Schema(type="Horror")
     *  )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested genre"
     * )
     *
     * @SWG\Tag(name="genres")
     *
     * @param Integer integer
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putGenreAction($id, Request $request)
    {
        $genre = $this->getDoctrine()->getRepository(Genre::class)->find($id);

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

    public function updateGenresAction() {
        return $this->createApiResponse("Can't update entire resource", Response::HTTP_METHOD_NOT_ALLOWED);

    }

    public function deleteGenresAction() {
        return $this->createApiResponse("Can't delete entire resource", Response::HTTP_METHOD_NOT_ALLOWED);

    }

    /**
     * Remove a genre
     *
     * Deletes an individual genre resource
     *
     * @SWG\Response(
     *     response=200,
     *     description="",
     * ),
     * @SWG\Response(
     *     response=404,
     *     description="ID not found or invalid",
     *     examples={
     *          "": "A genre with that ID could not be found"
     *     }
     * ),
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized Access",
     *     examples={
     *          "": "OAuth2 Authentication Required"
     *     }
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the genre to delete",
     * )
     *
     * @SWG\Tag(name="genres")
     *
     * @param Integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteGenreAction($id)
    {
        $genre = $this->getDoctrine()->getRepository(Genre::class)->find($id);

        if (!$genre) {
            return $this->createApiResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($genre);
        $em->flush();

        return $this->createApiResponse(null, Response::HTTP_NO_CONTENT);
    }
}


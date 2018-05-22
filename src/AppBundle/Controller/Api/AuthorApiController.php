<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 28/03/2018
 * Time: 22:52
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Author;
use AppBundle\Form\Api\AuthorApiType;
use AppBundle\Form\GenreType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

class AuthorApiController extends BaseApiController
{
    /**
     * Retrieve authors
     *
     * Returns a paginated collection of author resources
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of author resources",
     *     examples={
     *          "": {
     *                  { "id": "1", "name": "Stephen King", "biography": "Stephen Edwin King was born the second...", "avatar": "http://localhost:8003/uploads/authors/avatars...", "_self" : "{ href: /api/v1/authors/1 }" }
     *              },
     *          "Pagination Links": {
     *              "self": {
     *                  "href": "/api/v1/authors?page=1&limit=10"
     *              },
     *                  "first": {
     *                      "href": "/api/v1/authors?page=1&limit=10"
     *              },
     *                  "last": {
     *                      "href": "/api/v1/authors?page=2&limit=10"
     *              },
     *                  "next": {
     *                      "href": "/api/v1/authors?page=2&limit=10"
     *              }
     *          }
     *     }
     * )
     *
     * @SWG\Response(
     *     response=404,
     *     description="ID not found or invalid",
     *     examples={
     *          "": "An author with that ID could not be found"
     *     }
     * )
     *
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     default=10,
     *     required=false,
     *     description="The number of authors to return"
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     default=1,
     *     description="The page index of the paginated response"
     * )
     *
     * @SWG\Tag(name="authors")
     */
    public function getAuthorsAction(Request $request)
    {
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
        return $this->paginatedResponse($request, $authors, 'worth_reading_api_get_authors');
    }

    /**
     * Retrieve an author
     *
     * Retrieves an author resource based on a given id
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the author associated with the requested id",
     *     examples={
     *          "": {
     *                  { "id": "1", "name": "Stephen King", "biography": "Stephen Edwin King was born the second...", "avatar": "http://localhost:8003/uploads/authors/avatars...", "_self" : "{ href: /api/v1/authors/1 }" }
     *              },
     *     }
     * )
     *
     * @SWG\Response(
     *     response=404,
     *     description="ID not found or invalid",
     *     examples={
     *          "": "An author with that ID could not be found"
     *     }
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="The id of the author to retrieve",
     *     required=true,
     *     type="integer"
     * )
     * @SWG\Tag(name="authors")
     *
     * @param Integer integer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAuthorAction($id)
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);

        if(!$author) {
            return $this->createApiResponse($author, Response::HTTP_BAD_REQUEST);
        }

        return $this->createApiResponse($author);
    }

    /**
     * Create an author
     *
     * Creates an author resource
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
     *         description="The name of the author",
     *         in="body",
     *         name="name",
     *         required=true,
     *         @SWG\Schema(type="R.L. Stine")
     *  )
     *
     * @SWG\Parameter(
     *         description="A short bio of the author",
     *         in="body",
     *         name="biography",
     *         required=true,
     *         @SWG\Schema(type="Robert Lawrence Stine, sometimes known as Jovial Bob...")
     *  )
     *
     * @SWG\Parameter(
     *         description="A Base 64 encoding of the authors avatar",
     *         in="body",
     *         name="avatarFile",
     *         required=true,
     *         @SWG\Schema(type="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASAB...")
     *  )
     *
     * @SWG\Tag(name="authors")
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @return Response
     */
    public function postAuthorAction(Request $request)
    {
        $author = new Author();
        $form = $this->createForm(AuthorApiType::class, $author);

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

            $author->setAvatarFile(
                $this->convertBase64Image(
                    $this->getParameter('author_avatars_directory'),
                    $form->get("avatarFile")->getData()
                )
            );

            $author->setSignatureFile(
                $this->convertBase64Image(
                    $this->getParameter('author_signatures_directory'),
                    $form->get("signatureFile")->getData()
                )
            );

            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            // set the header to point to the new resource
            return $this->handleView($this->view(null, Response::HTTP_CREATED)
                ->setLocation($this->generateUrl('worth_reading_api_get_author', [
                    'id' => $author->getId()
                ])));

        } else {
            return $this->handleView($this->view($form , Response::HTTP_BAD_REQUEST));
        }
    }


    /**
     *
     * Update an Author
     *
     * Updates an author resource
     *
     * @SWG\Response(
     *     response=200,
     *     description="",
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
     *     description="An author with that id could not be found"
     * ),
     *
     * @SWG\Parameter(
     *         description="The name of the author",
     *         in="body",
     *         name="name",
     *         required=true,
     *         @SWG\Schema(type="R.L. Stine")
     *  )
     *
     * @SWG\Parameter(
     *         description="A short bio of the author",
     *         in="body",
     *         name="biography",
     *         required=true,
     *         @SWG\Schema(type="Robert Lawrence Stine, sometimes known as Jovial Bob...")
     *  )
     *
     * @SWG\Parameter(
     *         description="A Base 64 encoding of the authors avatar",
     *         in="body",
     *         name="avatarFile",
     *         required=true,
     *         @SWG\Schema(type="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASAB...")
     *  )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="The id of the author to retrieve",
     *     required=true,
     *     type="integer"
     * )
     *
     * @SWG\Tag(name="authors")
     * @Security(name="Bearer")
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function putAuthorAction($id, Request $request)
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);

        if(!$author) {
            return $this->createApiResponse("No author associated with this ID", 404);
        }

        $form = $this->createForm(AuthorApiType::class, $author);

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

            $author->setAvatarFile(
                $this->convertBase64Image(
                    $this->getParameter('author_avatars_directory'),
                    $form->get("avatarFile")->getData()
                )
            );

            $author->setSignatureFile(
                $this->convertBase64Image(
                    $this->getParameter('author_signatures_directory'),
                    $form->get("signatureFile")->getData()
                )
            );

            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            // set the header to point to the new resource
            return $this->handleView($this->view(null, Response::HTTP_CREATED)
                ->setLocation($this->generateUrl('worth_reading_api_get_author', [
                    'id' => $author->getId()
                ])));

        } else {
            return $this->handleView($this->view($form , Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Remove an author
     *
     * Deletes an individual author resource
     *
     * @SWG\Response(
     *     response=200,
     *     description="",
     * ),
     * @SWG\Response(
     *     response=404,
     *     description="ID not found or invalid",
     *     examples={
     *          "": "An author with that ID could not be found"
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
     *     description="The unique identifier of the author to delete",
     * )
     *
     * @SWG\Tag(name="authors")
     * @Security(name="Bearer")
     *
     * @param Integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAuthorAction($id)
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);

        if (!$author) {
            return $this->createApiResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($author);
        $em->flush();

        return $this->createApiResponse(null, Response::HTTP_NO_CONTENT);
    }
}
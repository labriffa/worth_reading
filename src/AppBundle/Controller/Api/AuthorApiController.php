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
     * List authors
     *
     * This call returns a collection of authors as a paginated response
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of authors",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Author::class),
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="id", type="integer", example="1"),
     *              @SWG\Property(property="name", type="string", example="Stephen King"),
     *              @SWG\Property(property="avatar", type="string", example="c5b347b4a08e402e0ca4f7d78f6ded82"),
     *              @SWG\Property(property="signature", type="string", example="0167b290f06defefb09d828313063c35"),
     *              @SWG\Property(property="biography", type="string", example="Stephen Edwin King was born the second son of Donald and Nellie Ruth Pillsbury King. After his...")
     *          )
     *     )
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="string",
     *     description="The field used to determine the number of authors returned"
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="string",
     *     description="The field used to resolve the requested page"
     * )
     * @SWG\Tag(name="authors")
     * @Security(name="Bearer")
     */
    public function getAuthorsAction(Request $request)
    {
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
        return $this->paginatedResponse($request, $authors, 'worth_reading_api_get_authors');
    }

    /**
     * Retrieve an author
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the author associated with the requested id",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Author::class),
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="id", type="integer", example="1"),
     *              @SWG\Property(property="name", type="string", example="Horror")
     *          )
     *     )
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="The id of the genre to retrieve",
     *     required=true,
     *     type="integer"
     * )
     * @SWG\Tag(name="authors")
     * @Security(name="Bearer")
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
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of genres",
     * )
     * @SWG\Tag(name="authors")
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
     * Remove an author
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of genres"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="The id of the genre to remove",
     *     required=true,
     *     type="integer"
     * )
     * @SWG\Tag(name="genres")
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
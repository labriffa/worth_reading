<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 29/03/2018
 * Time: 02:33
 */

namespace AppBundle\Controller\Api;

use AppBundle\AppBundle;
use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Form\Api\BookApiType;
use AppBundle\Form\GenreType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Route;

class BookApiController extends BaseApiController
{
    /**
     * List books
     *
     * This call returns a collection of genres as a paginated response
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of genres",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Book::class),
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="id", type="integer", example="1"),
     *              @SWG\Property(property="name", type="string", example="Horror")
     *          )
     *     )
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="string",
     *     description="The field used to determine the number of genres returned"
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="string",
     *     description="The field used to resolve the requested page"
     * )
     * @SWG\Tag(name="books")
     * @Security(name="Bearer")
     */
    public function getBooksAction(Request $request)
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();

        return $this->paginatedResponse(
            $request,
            $books,
            'worth_reading_api_get_books'
        );
    }

    /**
     * Retrieve a book
     *
     * This call returns a collection of genres as a paginated response
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of genres",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Book::class),
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="id", type="integer", example="1"),
     *              @SWG\Property(property="name", type="string", example="Horror")
     *          )
     *     )
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="string",
     *     description="The field used to determine the number of genres returned"
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="string",
     *     description="The field used to resolve the requested page"
     * )
     * @SWG\Tag(name="books")
     * @Security(name="Bearer")
     */
    public function getBookAction($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if(!$book) {
            return $this->createApiResponse(Response::HTTP_NOT_FOUND);
        }

        return $this->createApiResponse($book);
    }

    /**
     * Create a book
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of genres",
     * )
     * @SWG\Tag(name="genres")
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @return Response
     */
    public function postBookAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookApiType::class, $book);

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

            $book->setBookCoverFile(
                $this->convertBase64Image(
                    $this->getParameter('book_covers_directory'),
                    $form->get('bookCoverFile')->getData()
                )
            );

            // get current user
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $author_id = $form->get('authors')->getData();
            $genre_id = $form->get('genres')->getData();

            $author = $this->getDoctrine()->getRepository(Author::class)->find($author_id);
            $genre = $this->getDoctrine()->getRepository(Genre::class)->find($genre_id);

            if(!$author || !$genre) {
                return $this->handleView($this->view($form , Response::HTTP_BAD_REQUEST));
            }

            $book->addAuthor($author);
            $book->addGenre($genre);

            // set oauth user
            $book->setUser($user);

            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            // set the header to point to the new resource
            return $this->handleView($this->view(null, Response::HTTP_CREATED)
                ->setLocation($this->generateUrl('worth_reading_api_get_book', [
                    'id' => $book->getId()
                ])));

        } else {
            return $this->createApiResponse("dfdfdf", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete a book
     *
     * /**
     * Retrieve a book
     *
     * This call returns a collection of genres as a paginated response
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of genres",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Book::class),
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="id", type="integer", example="1"),
     *              @SWG\Property(property="name", type="string", example="Horror")
     *          )
     *     )
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="string",
     *     description="The field used to determine the number of genres returned"
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="string",
     *     description="The field used to resolve the requested page"
     * )
     * @SWG\Tag(name="books")
     * @Security(name="Bearer")
     *
     * @param $id
     * @return Response
     */
    public function deleteBookAction($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if(!$book) {
            return $this->createApiResponse("A book with that id doesn't exist", Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->createApiResponse(null);
    }


    /**
     * Disallow puts on entire collections
     *
     * @return Response
     */
    public function putBooks() {
        return $this->createApiResponse("Can't update entire resource", Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Disallow delete on entire collections
     *
     * @return Response
     */
    public function deleteBooks() {
        return $this->createApiResponse("Can't delete entire resource", Response::HTTP_METHOD_NOT_ALLOWED);
    }
}